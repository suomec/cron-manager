<?php

declare(strict_types=1);

namespace CronManager\Parsers;

use CronManager\Exceptions\DayIncorrectException;
use CronManager\Exceptions\HourIncorrectException;
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

    /**
     * @throws HourIncorrectException
     */
    protected function checkHour(int $hour): void
    {
        if ($hour < 0) {
            throw new HourIncorrectException("hour less than 0");
        }

        if ($hour > 23) {
            throw new HourIncorrectException("hour more than 23");
        }
    }

    /**
     * @throws DayIncorrectException
     */
    protected function checkDay(int $day): void
    {
        if ($day < 0) {
            throw new DayIncorrectException("day less than 0");
        }

        if ($day > 31) {
            throw new DayIncorrectException("day more than 31");
        }
    }
}
