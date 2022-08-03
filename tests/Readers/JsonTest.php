<?php

declare(strict_types=1);

namespace CronManager\Tests\Readers;

use CronManager\Exceptions\ConfigReadException;
use CronManager\Readers\Json;
use CronManager\Tests\Core\TestCase;

class JsonTest extends TestCase
{
    public function testReadFailsForIncorrectJson(): void
    {
        $reader = new Json();

        $this->expectException(ConfigReadException::class);
        $this->expectExceptionMessage("can't decode config file json");
        $reader->read('{{{');
    }

    public function testReadFailsIfNameKeyNotSet(): void
    {
        $reader = new Json();

        $this->expectException(ConfigReadException::class);
        $this->expectExceptionMessage("`name` key should be string");
        $reader->read('{}');
    }

    public function testReadFailsIfNameKeyNotString(): void
    {
        $reader = new Json();

        $this->expectException(ConfigReadException::class);
        $this->expectExceptionMessage("`name` key should be string");
        $reader->read('{"name": 123}');
    }

    public function testReadFailsIfStagesKeyNotSet(): void
    {
        $reader = new Json();

        $this->expectException(ConfigReadException::class);
        $this->expectExceptionMessage("`stages` key should be array");
        $reader->read('{"name": "name", "key": "test"}');
    }

    public function testReadFailsIfStagesKeyNotArray(): void
    {
        $reader = new Json();

        $this->expectException(ConfigReadException::class);
        $this->expectExceptionMessage("`stages` key should be array");
        $reader->read('{"name": "name", "key": "test", "stages": 123}');
    }

    public function testReadFailsIfTasksKeyNotSet(): void
    {
        $reader = new Json();

        $this->expectException(ConfigReadException::class);
        $this->expectExceptionMessage("`tasks` key should be array");
        $reader->read('{"name": "name", "key": "test", "stages": []}');
    }

    public function testReadFailsIfTasksKeyNotArray(): void
    {
        $reader = new Json();

        $this->expectException(ConfigReadException::class);
        $this->expectExceptionMessage("`tasks` key should be array");
        $reader->read('{"name": "name", "key": "test", "stages": [], "tasks": 123}');
    }

    public function testReadSuccess(): void
    {
        $reader = new Json();

        $config = $reader->read('{"name": "name", "key": "test", "stages": [
            {"name": "n1", "variables": {"k1": "v1", "k2": "v2"}}
        ], "tasks": [
            {"name": "t1", "is_enabled": true, "stages": [], "schedule": "* * * * *", "command": "/bin/bash"}
        ]}');

        $this->assertEquals("name", $config->getName());

        $this->assertEquals("n1", $config->getStages()[0]->getName());
        $this->assertEquals("v1", $config->getStages()[0]->getVariables()["k1"]);
        $this->assertEquals("v2", $config->getStages()[0]->getVariables()["k2"]);

        $this->assertEquals("t1", $config->getTasks()[0]->getName());
    }
}
