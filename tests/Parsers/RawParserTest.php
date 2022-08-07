<?php

declare(strict_types=1);

namespace CronManager\Tests\Parsers;

use CronManager\Parsers\RawParser;
use CronManager\Tests\Core\TestCase;

class RawParserTest extends TestCase
{
    public function testEveryMinuteParserSuccess(): void
    {
        $this->assertEquals('some expression', (new RawParser())->parse('raw:some expression'));
    }
}
