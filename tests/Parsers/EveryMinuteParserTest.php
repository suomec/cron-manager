<?php

declare(strict_types=1);

namespace CronManager\Tests\Parsers;

use CronManager\Parsers\EveryMinuteParser;
use CronManager\Tests\Core\TestCase;

class EveryMinuteParserTest extends TestCase
{
    public function testEveryMinuteParserSuccess(): void
    {
        $this->assertEquals('* * * * *', (new EveryMinuteParser())->parse('every minute'));
    }
}
