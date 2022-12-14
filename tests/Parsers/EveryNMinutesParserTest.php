<?php

declare(strict_types=1);

namespace CronManager\Tests\Parsers;

use CronManager\Exceptions\MinuteIncorrectException;
use CronManager\Parsers\EveryNMinutesParser;
use CronManager\Tests\Core\TestCase;

class EveryNMinutesParserTest extends TestCase
{
    /**
     * @dataProvider providerSuccess
     */
    public function testSuccess(string $in, string $out): void
    {
        $parser = new EveryNMinutesParser();

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

        (new EveryNMinutesParser())->parse($in);
    }

    /**
     * @return array<mixed>
     */
    public function providerSuccess(): array
    {
        return [
            ['every 1 minute', '*/1 * * * *'],
            ['every 5 minutes', '*/5 * * * *'],
            ['every 10 minutes', '*/10 * * * *'],
        ];
    }

    /**
     * @return array<mixed>
     */
    public function providerFails(): array
    {
        return [
            ['every 66 minute', MinuteIncorrectException::class, 'minute more than 59'],
            ['every 0 minute', MinuteIncorrectException::class, 'minute is zero'],
        ];
    }
}
