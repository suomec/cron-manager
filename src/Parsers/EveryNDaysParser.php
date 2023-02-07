<?php

declare(strict_types=1);

namespace CronManager\Parsers;

use CronManager\Exceptions\DayIncorrectException;

/**
 * Template: `every N days`
 */
class EveryNDaysParser extends Base
{
    public function parse(string $raw): ?string
    {
        preg_match("|every (\d+) days?|", $raw, $matches);

        if (!isset($matches[1])) {
            return null;
        }

        $day = (int)$matches[1];
        $this->checkDay($day);

        if ($day === 0) {
            throw new DayIncorrectException("day is zero");
        }

        return sprintf('0 0 */%d * *', $day);
    }
}
