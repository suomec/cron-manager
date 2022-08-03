<?php

declare(strict_types=1);

namespace CronManager\Interfaces;

use CronManager\Objects\Config;

/**
 * Converts config file contents to Config object
 */
interface Reader
{
    public function read(string $contents): Config;
}
