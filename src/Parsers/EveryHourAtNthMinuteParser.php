<?php

declare(strict_types=1);

namespace CronManager\Parsers;

use CronManager\Exceptions\MinuteIncorrectException;

/**
 * Template: `every hour at Nth minute`
 */
class EveryHourAtNthMinuteParser extends Base
{
    public function parse(string $raw): ?string
    {
        preg_match("!every hour at (\d+)(th|nd)? minutes?!", $raw, $matches);

        if (!isset($matches[1])) {
            return null;
        }

        $minute = (int)$matches[1];
        $this->checkMinute($minute);

        if ($minute === 0) {
            throw new MinuteIncorrectException("minute is zero");
        }

        return sprintf('%d * * * *', $minute);
    }
}
