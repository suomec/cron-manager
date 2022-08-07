<?php

declare(strict_types=1);

namespace CronManager\Tests\Parsers;

use CronManager\Exceptions\MinuteIncorrectException;
use CronManager\Parsers\EveryHourAtNthMinuteParser;
use CronManager\Tests\Core\TestCase;

class EveryHourAtNthMinuteParserTest extends TestCase
{
    /**
     * @dataProvider providerSuccess
     */
    public function testSuccess(string $in, string $out): void
    {
        $parser = new EveryHourAtNthMinuteParser();

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

        (new EveryHourAtNthMinuteParser())->parse($in);
    }

    /**
     * @return array<mixed>
     */
    public function providerSuccess(): array
    {
        return [
            ['every hour at 1 minute', '1 * * * *'],
            ['every hour at 2nd minute', '2 * * * *'],
            ['every hour at 20th minute', '20 * * * *'],
            ['every hour at 10 minute', '10 * * * *'],
            ['every hour at 5 minutes', '5 * * * *'],
        ];
    }

    /**
     * @return array<mixed>
     */
    public function providerFails(): array
    {
        return [
            ['every hour at 666 minute', MinuteIncorrectException::class, 'minute more than 59'],
            ['every hour at 0 minute', MinuteIncorrectException::class, 'minute is zero'],
        ];
    }
}
