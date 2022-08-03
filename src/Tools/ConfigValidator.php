<?php

declare(strict_types=1);

namespace CronManager\Tools;

use CronManager\Exceptions\ConfigValidatorException;
use CronManager\Objects\Config;
use CronManager\Objects\ConfigStage;

class ConfigValidator
{
    /**
     * @param Config $config
     * @throws ConfigValidatorException
     */
    public static function validate(Config $config): void
    {
        // every task stage should be described
        $stages = array_map(function (ConfigStage $s) {
            return $s->getName();
        }, $config->getStages());

        foreach ($config->getTasks() as $task) {
            foreach ($task->getStagesNames() as $taskStage) {
                if (!in_array($taskStage, $stages, true)) {
                    throw new ConfigValidatorException(
                        "stage `{$taskStage}` not found in stages list: " . implode(', ', $stages)
                    );
                }
            }
        }

        // same keys set for all stages
        $vKeys = [];
        foreach ($config->getStages() as $stage) {
            $keys = array_keys($stage->getVariables());
            sort($keys);
            $vKeys[md5(serialize($keys))] = true;
        }

        if (count($vKeys) > 1) {
            throw new ConfigValidatorException("stages keys sets are different");
        }
    }
}
