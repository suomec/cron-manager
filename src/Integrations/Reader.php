<?php

declare(strict_types=1);

namespace CronManager\Integrations;

use CronManager\Exceptions\ConfigReadException;
use CronManager\Objects\Config;
use CronManager\Readers\Json;

class Reader
{
    /**
     * @param string $path Path to config file
     * @return Config
     * @throws ConfigReadException
     */
    public static function readConfig(string $path): Config
    {
        if (!file_exists($path)) {
            throw new ConfigReadException("file not found: {$path}");
        }

        $config = file_get_contents($path);
        if (!is_string($config)) {
            throw new ConfigReadException("can't read config file: {$path}");
        }

        // json only
        return (new Json())->read($config);
    }
}
