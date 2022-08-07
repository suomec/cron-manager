<?php

declare(strict_types=1);

namespace CronManager\Parsers;

/**
 * Template: `every day at HH:MM`
 */
class EveryDayAtSpecificTimeParser extends Base
{
    public function parse(string $raw): ?string
    {
        preg_match("|every day at (\d+):(\d+)|", $raw, $matches);

        if (!isset($matches[2])) {
            return null;
        }

        $hour = (int)$matches[1];
        $this->checkHour($hour);

        $minute = (int)$matches[2];
        $this->checkMinute($minute);

        return sprintf('%d %d * * *', $minute, $hour);
    }
}
