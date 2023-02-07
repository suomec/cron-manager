<?php

declare(strict_types=1);

namespace CronManager\Readers;

use CronManager\Exceptions\ConfigReadException;
use CronManager\Interfaces\Reader;
use CronManager\Objects\Config;
use CronManager\Objects\ConfigStage;
use CronManager\Objects\ConfigTask;

class Json implements Reader
{
    /**
     * @throws ConfigReadException
     */
    public function read(string $contents): Config
    {
        $result = json_decode($contents, true, 10);
        if (!is_array($result)) {
            throw new ConfigReadException("can't decode config file json");
        }

        if (!isset($result['name']) || !is_string($result['name'])) {
            throw new ConfigReadException("`name` key should be string");
        }

        if (!isset($result['key']) || !is_string($result['key'])) {
            throw new ConfigReadException("`key` key should be string");
        }

        if (!isset($result['stages']) || !is_array($result['stages'])) {
            throw new ConfigReadException("`stages` key should be array");
        }

        $stages = [];
        foreach ($result['stages'] as $stage) {
            $stages[] = new ConfigStage($stage['name'], $stage['variables']);
        }

        if (!isset($result['tasks']) || !is_array($result['tasks'])) {
            throw new ConfigReadException("`tasks` key should be array");
        }

        $tasks = [];
        foreach ($result['tasks'] as $task) {
            $tasks[] = new ConfigTask(
                $task['name'],
                $task['is_enabled'],
                $task['stages'],
                $task['schedule'],
                $task['command'],
                $task['parallel'] ?? []
            );
        }

        return new Config($result['name'], $result['key'], $stages, $tasks);
    }
}
