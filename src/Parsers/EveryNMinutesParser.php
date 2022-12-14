<?php

declare(strict_types=1);

namespace CronManager\Parsers;

use CronManager\Exceptions\MinuteIncorrectException;

/**
 * Template: `every N minute`
 */
class EveryNMinutesParser extends Base
{
    public function parse(string $raw): ?string
    {
        preg_match("|every (\d+) minutes?|", $raw, $matches);

        if (!isset($matches[1])) {
            return null;
        }

        $minute = (int)$matches[1];
        $this->checkMinute($minute);

        if ($minute === 0) {
            throw new MinuteIncorrectException("minute is zero");
        }

        return sprintf('*/%d * * * *', $minute);
    }
}
