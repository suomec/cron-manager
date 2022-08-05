<?php

declare(strict_types=1);

namespace CronManager\Interfaces;

/**
 * Processing human-readable formats like `every 5 minutes` to actual cron config '1 * * * *'
 */
interface Parser
{
    /**
     * Replaces human-readable phrase to crontab star-config
     * @param string $raw Phrase
     * @return string|null Config or null if it can't be applied
     */
    public function parse(string $raw): ?string;
}
