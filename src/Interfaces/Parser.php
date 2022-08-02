<?php

declare(strict_types=1);

namespace CronManager\Interfaces;

/**
 * Processing human-readable formats like `every 5 minutes` to actual cron config '1 * * * *'
 */
interface Parser
{
    public function parse(string $raw): ?string;
}
