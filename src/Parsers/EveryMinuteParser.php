<?php

declare(strict_types=1);

namespace CronManager\Parsers;

/**
 * Template: `every minute`
 */
class EveryMinuteParser extends Base
{
    public function parse(string $raw): ?string
    {
        if ($raw === 'every minute') {
            return '* * * * *';
        }

        return null;
    }
}
