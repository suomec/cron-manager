<?php

declare(strict_types=1);

namespace CronManager\Interfaces;

/**
 * Managing raw crontab strings
 */
interface Installer
{
    /**
     * @return string[]
     */
    public function get(): array;

    /**
     * @param string[] $lines
     * @return void
     */
    public function set(array $lines): void;
}
