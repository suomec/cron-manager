<?php

declare(strict_types=1);

namespace CronManager\Parsers;

use CronManager\Exceptions\MinuteIncorrectException;
use CronManager\Interfaces\Parser;

abstract class Base implements Parser
{
    /**
     * @throws MinuteIncorrectException
     */
    protected function checkMinute(int $minute): void
    {
        if ($minute < 0) {
            throw new MinuteIncorrectException("minute less than 0");
        }

        if ($minute > 59) {
            throw new MinuteIncorrectException("minute more than 59");
        }
    }
}
