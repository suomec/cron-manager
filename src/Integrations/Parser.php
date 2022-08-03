<?php

declare(strict_types=1);

namespace CronManager\Integrations;

use CronManager\Exceptions\ParserException;
use CronManager\Interfaces\Parser as ParserInterface;
use CronManager\Objects\ConfigStage;
use CronManager\Objects\ConfigTask;
use CronManager\Objects\CrontabLine;

class Parser
{
    /**
     * @param ParserInterface[] $parsers
     * @param ConfigTask $task
     * @param ConfigStage $stage
     * @return CrontabLine
     * @throws ParserException
     */
    public static function applyParsers(array $parsers, ConfigTask $task, ConfigStage $stage): CrontabLine
    {
        $rawSchedule = trim($task->getSchedule());
        $rawSchedule = preg_replace('|\s+|', ' ', $rawSchedule);
        if (!is_string($rawSchedule)) {
            throw new ParserException("schedule string is incorrect");
        }
        $rawSchedule = strtolower($rawSchedule);

        $schedule = null;
        foreach ($parsers as $parser) {
            $schedule = $parser->parse($rawSchedule);
            if (is_string($schedule)) {
                break;
            }
        }

        // parser not applied
        if (!is_string($schedule)) {
            throw new ParserException("can't parse schedule `{$task->getSchedule()}`");
        }

        // real command with {variables}
        $command = $task->getCommand();
        foreach ($stage->getVariables() as $k => $v) {
            $command = str_replace('{' . $k . '}', $v, $command);
        }

        return new CrontabLine($task->getName(), sprintf('%s %s', $schedule, $command));
    }

    /**
     * @return ParserInterface[]
     * @throws ParserException
     */
    public static function getParsers(): array
    {
        $classFiles = glob(__DIR__ . '/../Parsers/*Parser.php');
        if (!is_array($classFiles)) {
            throw new ParserException("can't load parser engines");
        }

        $parsers = [];

        foreach ($classFiles as $classFile) {
            $classFile = realpath($classFile);
            if (!is_string($classFile)) {
                continue;
            }

            $className = basename(str_replace('.php', '', $classFile));
            $class = '\\CronManager\\Parsers\\' . $className;

            $parsers[] = new $class();
        }

        /** @var ParserInterface[] $parsers */
        return $parsers;
    }
}
