<?php

declare(strict_types=1);

namespace CronManager\Tests\Parsers;

use CronManager\Exceptions\DayIncorrectException;
use CronManager\Parsers\EveryNDaysParser;
use CronManager\Tests\Core\TestCase;

class EveryNDaysParserTest extends TestCase
{
    /**
     * @dataProvider providerSuccess
     */
    public function testSuccess(string $in, string $out): void
    {
        $parser = new EveryNDaysParser();

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

        (new EveryNDaysParser())->parse($in);
    }

    /**
     * @return array<mixed>
     */
    public function providerSuccess(): array
    {
        return [
            ['every 1 day', '0 0 */1 * *'],
            ['every 5 days', '0 0 */5 * *'],
            ['every 10 days', '0 0 */10 * *'],
        ];
    }

    /**
     * @return array<mixed>
     */
    public function providerFails(): array
    {
        return [
            ['every 38 days', DayIncorrectException::class, 'day more than 31'],
            ['every 0 day', DayIncorrectException::class, 'day is zero'],
        ];
    }
}
