<?php

declare(strict_types=1);

namespace CronManager\Integrations;

use CronManager\Tools\ConfigValidator;
use CronManager\Exceptions\ConfigValidatorException;
use CronManager\Exceptions\ParserException;
use CronManager\Objects\Config;
use CronManager\Objects\ConfigStage;
use CronManager\Objects\Crontab;

class Generator
{
    /**
     * @param Config $config
     * @param string $stageName
     * @return Crontab
     * @throws ParserException|ConfigValidatorException
     */
    public static function generateCrontab(Config $config, string $stageName): Crontab
    {
        ConfigValidator::validate($config);

        $userStage = self::getStage($config, $stageName);

        $parsers = Parser::getParsers();

        $lines = [];
        foreach ($config->getTasks() as $task) {
            if (!$task->isEnabled()) {
                continue;
            }

            if (!in_array($userStage->getName(), $task->getStagesNames(), true)) {
                continue;
            }

            $lines[] = Parser::applyParsers($parsers, $task, $userStage);
        }

        return new Crontab($config->getName(), $config->getKey(), $lines);
    }

    /**
     * @throws ParserException
     */
    private static function getStage(Config $config, string $stageName): ConfigStage
    {
        $userStages = array_filter($config->getStages(), function (ConfigStage $s) use ($stageName) {
            if ($s->getName() === $stageName) {
                return true;
            }

            return false;
        });

        if (count($userStages) === 0) {
            throw new ParserException("stage not found: {$stageName}");
        }

        /** @var ConfigStage $userStage */
        $userStage = array_values($userStages)[0];

        return $userStage;
    }
}
