<?php

declare(strict_types=1);

namespace CronManager\Parsers;

use CronManager\Exceptions\HourIncorrectException;

/**
 * Template: `every N hours`
 */
class EveryNHoursParser extends Base
{
    public function parse(string $raw): ?string
    {
        preg_match("|every (\d+) hours?|", $raw, $matches);

        if (!isset($matches[1])) {
            return null;
        }

        $hour = (int)$matches[1];
        $this->checkHour($hour);

        if ($hour === 0) {
            throw new HourIncorrectException("hour is zero");
        }

        return sprintf('0 */%d * * *', $hour);
    }
}
