<?php

declare(strict_types=1);

namespace CronManager\Interfaces;

/**
 * Managing raw crontab strings for specific OS
 */
interface Os
{
    /**
     * Reads file to string
     * @param string $path
     * @return string File contents
     */
    public function readFile(string $path): string;

    /**
     * @return string[] Crontab lines
     */
    public function getCrontab(): array;

    /**
     * @param string[] $lines New lines
     * @return void
     */
    public function setCrontab(array $lines): void;
}
