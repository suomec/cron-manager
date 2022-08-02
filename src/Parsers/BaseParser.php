<?php

declare(strict_types=1);

namespace CronManager\Parsers;

use CronManager\Exceptions\MinuteExceedsMaximumException;
use CronManager\Exceptions\MinuteLessThanZeroException;
use CronManager\Interfaces\Parser;

class BaseParser implements Parser
{
    public function parse(string $raw): ?string
    {
        throw new \Exception("Not implemented");
    }

    protected function checkMinute(int $minute): void
    {
        if ($minute < 0) {
            throw new MinuteLessThanZeroException();
        }

        if ($minute > 59) {
            throw new MinuteExceedsMaximumException();
        }
    }
}
