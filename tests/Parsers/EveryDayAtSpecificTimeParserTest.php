<?php

declare(strict_types=1);

namespace CronManager\Tests\Parsers;

use CronManager\Exceptions\HourIncorrectException;
use CronManager\Exceptions\MinuteIncorrectException;
use CronManager\Parsers\EveryDayAtSpecificTimeParser;
use CronManager\Tests\Core\TestCase;

class EveryDayAtSpecificTimeParserTest extends TestCase
{
    /**
     * @dataProvider providerSuccess
     */
    public function testSuccess(string $in, string $out): void
    {
        $parser = new EveryDayAtSpecificTimeParser();

        $result = $parser->parse($in);

        $this->assertEquals($out, $result);
    }

    /**
     * @dataProvider providerFails
     */
    public function testFails(string $in, string $exceptionClass, string $exceptionMessage): void
    {
        $this->expectException($exceptionClass);// @phpstan-ignore-line
        $this->expectExceptionMessage($exceptionMessage);

        (new EveryDayAtSpecificTimeParser())->parse($in);
    }

    /**
     * @return array<mixed>
     */
    public function providerSuccess(): array
    {
        return [
            ['every day at 02:03', '3 2 * * *'],
            ['every day at 12:10', '10 12 * * *'],
            ['every day at 4:45', '45 4 * * *'],
            ['every day at 0:00', '0 0 * * *'],
        ];
    }

    /**
     * @return array<mixed>
     */
    public function providerFails(): array
    {
        return [
            ['every day at 45:22', HourIncorrectException::class, 'hour more than 23'],
            ['every day at 22:66', MinuteIncorrectException::class, 'minute more than 59'],
        ];
    }
}
