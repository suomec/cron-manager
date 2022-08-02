<?php

declare(strict_types=1);

namespace CronManager\Tests\Parsers;

use CronManager\Exceptions\MinuteExceedsMaximumException;
use CronManager\Parsers\EveryNMinutes;
use CronManager\Tests\TestCase;

class EveryNMinutesTest extends TestCase
{
    /**
     * @dataProvider providerSuccess
     */
    public function testSuccess(string $in, string $out): void
    {
        $parser = new EveryNMinutes();

        $result = $parser->parse($in);

        $this->assertEquals($out, $result);
    }

    public function testFailsWhenMinuteTooBig(): void
    {
        $this->expectException(MinuteExceedsMaximumException::class);

        (new EveryNMinutes())->parse('every 66 minute');
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
