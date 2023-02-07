<?php

declare(strict_types=1);

namespace CronManager;

use CronManager\Exceptions\IntegrationException;
use CronManager\Interfaces\Os;
use CronManager\Interfaces\Parser;
use CronManager\Objects\Config;
use CronManager\Objects\ConfigStage;
use CronManager\Objects\ConfigTask;
use CronManager\Objects\Crontab;
use CronManager\Objects\CrontabLine;
use CronManager\Parsers\EveryDayAtSpecificTimeParser;
use CronManager\Parsers\EveryHourAtNthMinuteParser;
use CronManager\Parsers\EveryMinuteParser;
use CronManager\Parsers\EveryNDaysParser;
use CronManager\Parsers\EveryNHoursParser;
use CronManager\Parsers\EveryNMinutesParser;
use CronManager\Parsers\RawParser;
use CronManager\Readers\Json;

class Integration
{
    /** @var Parser[] */
    private array $parsers;

    /**
     * @param Parser[]|null $overrideParsers Replace for default parsers list
     */
    public function __construct(?array $overrideParsers = null)
    {
        if (is_array($overrideParsers)) {
            $parsers = $overrideParsers;
        } else {
            $parsers = [
                new EveryDayAtSpecificTimeParser(),
                new EveryHourAtNthMinuteParser(),
                new EveryMinuteParser(),
                new EveryNDaysParser(),
                new EveryNHoursParser(),
                new EveryNMinutesParser(),
                new RawParser(),
            ];
        }

        $this->parsers = $parsers;
    }

    // main method
    public function parseJSONConfigAndInstall(Os $os, string $configPath, ?string $stageName): void
    {
        $config = (new Json())->read($os->readFile($configPath));

        $config = $this->validateConfig($config);

        $crontab = $this->generateCrontab($config, $stageName);

        $this->installCrontab($crontab, $os);
    }

    /**
     * @param Config $config
     * @return Config
     */
    public function validateConfig(Config $config): Config
    {
        // every task stage should be described
        $stages = array_map(function (ConfigStage $s) {
            return $s->getName();
        }, $config->getStages());

        foreach ($config->getTasks() as $task) {
            foreach ($task->getStagesNames() as $taskStage) {
                if (!in_array($taskStage, $stages, true)) {
                    throw new IntegrationException(
                        "stage `{$taskStage}` not found in stages list: " . implode(', ', $stages)
                    );
                }
            }
        }

        // same keys sets for all stages
        $vKeys = [];
        foreach ($config->getStages() as $stage) {
            $keys = array_keys($stage->getVariables());
            sort($keys);
            $vKeys[md5(serialize($keys))] = true;
        }

        if (count($vKeys) > 1) {
            throw new IntegrationException("stages keys sets are different");
        }

        // reserved variables names
        $reservedVariablesNames = ['parallel'];
        $reservedVariablesNamesStr = implode(', ', $reservedVariablesNames);
        foreach ($config->getStages() as $stage) {
            $stageVarsNames = array_keys($stage->getVariables());
            if (array_intersect($reservedVariablesNames, $stageVarsNames) !== []) {
                throw new IntegrationException(
                    "stages keys sets contains reserved variables names: {$reservedVariablesNamesStr}"
                );
            }
        }

        return $config;
    }

    /**
     * Creates crontab object from config for specific stage
     * @param Config $config
     * @param string|null $stageName
     * @return Crontab
     * @throws IntegrationException
     */
    public function generateCrontab(Config $config, ?string $stageName): Crontab
    {
        $userStage = $this->detectStage($config, $stageName);

        $lines = [];
        foreach ($config->getTasks() as $task) {
            if (!$task->isEnabled()) {
                continue;
            }

            if (!in_array($userStage->getName(), $task->getStagesNames(), true)) {
                continue;
            }

            $lines = array_merge($lines, $this->applyParsers($task, $userStage));
        }

        return new Crontab($config->getName(), $config->getKey(), $lines);
    }

    /**
     * Check every parser for task schedule description
     * @param ConfigTask $task
     * @param ConfigStage $stage
     * @return CrontabLine[]
     */
    public function applyParsers(ConfigTask $task, ConfigStage $stage): array
    {
        $rawSchedule = trim($task->getSchedule());
        $rawSchedule = preg_replace('|\s+|', ' ', $rawSchedule);
        if (!is_string($rawSchedule)) {
            throw new IntegrationException("schedule string is incorrect");
        }
        $rawSchedule = strtolower($rawSchedule);

        $schedule = null;
        foreach ($this->parsers as $parser) {
            $schedule = $parser->parse($rawSchedule);
            if (is_string($schedule)) {
                break;
            }
        }

        // parser not applied
        if (!is_string($schedule)) {
            throw new IntegrationException("can't parse expression `{$task->getSchedule()}`");
        }

        // real command with {variables}
        $command = $task->getCommand();
        foreach ($stage->getVariables() as $k => $v) {
            $command = str_replace('{' . $k . '}', $v, $command);
        }

        if (count($task->getParallel()) === 0) {
            return [new CrontabLine($task->getName(), sprintf('%s %s', $schedule, $command))];
        }

        // parallel - multiple lines from one command
        $lines = [];
        foreach ($task->getParallel() as $arg) {
            if (strpos($command, '{parallel}') === false) {
                throw new IntegrationException("command `{$command}` should contain {parallel} substring");
            }

            $tmp = $command;
            $tmp = str_replace('{parallel}', $arg, $tmp);

            $lines[] = new CrontabLine($task->getName(), sprintf('%s %s', $schedule, $tmp));
        }

        return $lines;
    }

    /**
     * Loads stage data from config by stage name
     * @param Config $config
     * @param string|null $stageName
     * @return ConfigStage
     */
    private function detectStage(Config $config, ?string $stageName): ConfigStage
    {
        $stages = $config->getStages();

        if ($stageName === null && count($stages) === 1) {
            return $stages[0];
        }

        foreach ($stages as $stage) {
            if ($stage->getName() === $stageName) {
                return $stage;
            }
        }

        throw new IntegrationException("stage not found: {$stageName}");
    }

    /**
     * Set crontab lines from new installation
     * @param Crontab $crontab
     * @param Os $os
     * @return void
     */
    public function installCrontab(Crontab $crontab, Os $os): void
    {
        // search for previous installation
        $iStart = $iEnd = 0;
        $hasStart = $hasEnd = false;

        $hash = md5($crontab->getKey());
        $oldLines = $os->getCrontab();

        foreach ($oldLines as $idx => $line) {
            if (strpos($line, $hash) === false) {
                continue;
            }

            if (!$hasStart) {
                $hasStart = true;
                $iStart = $idx;

                continue;
            }

            $hasEnd = true;
            $iEnd = $idx;

            break;
        }

        if ($hasStart && !$hasEnd) {
            throw new IntegrationException("can't find end of previous installation, fix crontab manually");
        }

        $newLines = array_merge(
            [sprintf("### %s (%s) DO NOT EDIT", $crontab->getName(), $hash)],
            $crontab->generateLines(),
            [sprintf("### FINISH %s DO NOT EDIT", $hash)],
        );

        //@phpstan-ignore-next-line
        if ($hasStart && $hasEnd) {
            // replace between start and finish indexes
            array_splice($oldLines, $iStart, ($iEnd - $iStart) + 1, $newLines);

            $toInstall = $oldLines;
        } else {
            // insert after old
            $toInstall = array_merge(
                $oldLines,
                $newLines,
            );
        }

        $os->setCrontab($toInstall);
    }
}
