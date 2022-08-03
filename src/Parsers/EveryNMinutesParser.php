<?php

declare(strict_types=1);

namespace CronManager\Parsers;

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

        return sprintf('*/%d * * * *', $minute);
    }
}
