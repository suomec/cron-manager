<?php

declare(strict_types=1);

namespace CronManager\Tests\Parsers;

use CronManager\Exceptions\HourIncorrectException;
use CronManager\Parsers\EveryNHoursParser;
use CronManager\Tests\Core\TestCase;

class EveryNHoursParserTest extends TestCase
{
    /**
     * @dataProvider providerSuccess
     */
    public function testSuccess(string $in, string $out): void
    {
        $parser = new EveryNHoursParser();

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

        (new EveryNHoursParser())->parse($in);
    }

    /**
     * @return array<mixed>
     */
    public function providerSuccess(): array
    {
        return [
            ['every 1 hour', '0 */1 * * *'],
            ['every 5 hours', '0 */5 * * *'],
            ['every 10 hours', '0 */10 * * *'],
        ];
    }

    /**
     * @return array<mixed>
     */
    public function providerFails(): array
    {
        return [
            ['every 28 hours', HourIncorrectException::class, 'hour more than 23'],
            ['every 0 hour', HourIncorrectException::class, 'hour is zero'],
        ];
    }
}
