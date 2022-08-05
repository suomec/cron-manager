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

    public function testFailsWhenMinuteTooBig(): void
    {
        $this->expectException(MinuteIncorrectException::class);
        $this->expectExceptionMessage("minute more than 59");

        (new EveryNMinutesParser())->parse('every 66 minute');
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
}
