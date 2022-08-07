<?php

declare(strict_types=1);

namespace CronManager\Tests;

use CronManager\Exceptions\IntegrationException;
use CronManager\Integration;
use CronManager\Interfaces\Os;
use CronManager\Interfaces\Parser;
use CronManager\Objects\Config;
use CronManager\Objects\ConfigStage;
use CronManager\Objects\ConfigTask;
use CronManager\Objects\Crontab;
use CronManager\Tests\Core\TestCase;

class IntegrationTest extends TestCase
{
    /** @var array<mixed> */
    public static array $tmpArray;
    public static string $tmpString;

    public function testValidateConfigFailsIfStageInOneOfTaskIsUnknown(): void
    {
        $config = new Config('', '', [
            new ConfigStage('known', []),
        ], [
            new ConfigTask('', true, ['known', 'unknown'], '', ''),
        ]);

        $this->expectException(IntegrationException::class);
        $this->expectExceptionMessage("stage `unknown` not found in stages list: known");
        (new Integration())->validateConfig($config);
    }

    public function testValidateConfigFailsIfStagesContainDifferentKeysSets(): void
    {
        $config = new Config('', '', [
            new ConfigStage('stage1', ['k1' => 'k1', 'k2' => 'k2']),
            new ConfigStage('stage1', ['k1' => 'k1', 'k2' => 'k2', 'k3' => 'k3']),
        ], []);

        $this->expectException(IntegrationException::class);
        $this->expectExceptionMessage("stages keys sets are different");
        (new Integration())->validateConfig($config);
    }

    public function testInstallCrontabSuccessForNewLines(): void
    {
        $os = $this->createMock(Os::class);
        $os->method('getCrontab')->willReturn([]);
        $os->method('setCrontab')->willReturnCallback(function (array $lines) {
            self::$tmpArray = $lines;
        });

        (new Integration())->installCrontab(
            new Crontab('name', 'key', []),
            $os,
        );

        $this->assertEquals([
            '### name (3c6e0b8a9c15224a8228b9a98ca1531d) DO NOT EDIT',
            '### FINISH 3c6e0b8a9c15224a8228b9a98ca1531d DO NOT EDIT',
        ], self::$tmpArray);
    }

    public function testInstallCrontabFailsIfPreviousInstallationBroken(): void
    {
        $os = $this->createMock(Os::class);
        $os->method('getCrontab')->willReturn([
            '### name (3c6e0b8a9c15224a8228b9a98ca1531d) DO NOT EDIT',
        ]);

        $this->expectException(IntegrationException::class);
        $this->expectExceptionMessage("can't find end of previous installation, fix crontab manually");
        (new Integration())->installCrontab(
            new Crontab('name', 'key', []),
            $os,
        );
    }

    public function testInstallCrontabSuccessForOldInstallation(): void
    {
        $os = $this->createMock(Os::class);
        $os->method('getCrontab')->willReturn([
            'line 001',
            'line 002',
            '### name (3c6e0b8a9c15224a8228b9a98ca1531d) DO NOT EDIT',
            'line 003',
            'line 004',
            '### FINISH 3c6e0b8a9c15224a8228b9a98ca1531d DO NOT EDIT',
            'line 005',
        ]);
        $os->method('setCrontab')->willReturnCallback(function (array $lines) {
            self::$tmpArray = $lines;
        });

        (new Integration())->installCrontab(
            new Crontab('name', 'key', []),
            $os,
        );

        $this->assertEquals([
            'line 001',
            'line 002',
            '### name (3c6e0b8a9c15224a8228b9a98ca1531d) DO NOT EDIT',
            '### FINISH 3c6e0b8a9c15224a8228b9a98ca1531d DO NOT EDIT',
            'line 005',
        ], self::$tmpArray);
    }

    public function testApplyParsersFailsIfNoParsersMatched(): void
    {
        $i = new Integration([]);

        $this->expectException(IntegrationException::class);
        $this->expectExceptionMessage("can't parse expression `SCHEDULE`");
        $i->applyParsers(
            new ConfigTask('', true, [], 'SCHEDULE', ''),
            new ConfigStage('', []),
        );
    }

    public function testApplyParsersSuccess(): void
    {
        $parser = $this->createMock(Parser::class);
        $parser->method('parse')->willReturnCallback(function (string $in) {
            self::$tmpString = $in;

            return 'NEW';
        });

        $i = new Integration([$parser]);

        $line = $i->applyParsers(
            new ConfigTask('NAME', true, [], '  TaSK  SCHEDULE  ', 'COMMAND {k1} {k2} {k1}'),
            new ConfigStage('', [
                'k1' => 'v1',
                'k2' => 'v2',
            ]),
        );

        $this->assertEquals('NAME', $line->getComment());
        $this->assertEquals('NEW COMMAND v1 v2 v1', $line->getCommand());

        $this->assertEquals('task schedule', self::$tmpString);
    }

    public function testGenerateCrontabSuccess(): void
    {
        $parser = $this->createMock(Parser::class);
        $parser->method('parse')->willReturn('PARSED');

        $i = new Integration([$parser]);

        $crontab = $i->generateCrontab(new Config('name', 'key', [
            new ConfigStage('stage', []),
        ], [
            new ConfigTask('CT1', true, ['test', 'stage', 'third'], '', ''),
            new ConfigTask('CT2', false, ['test', 'stage', 'third'], '', ''),// skip
            new ConfigTask('CT3', true, ['test', 'second'], '', ''),// skip
        ]), 'stage');

        $lines = $crontab->getLines();

        $this->assertEquals('name', $crontab->getName());
        $this->assertEquals('key', $crontab->getKey());
        $this->assertCount(1, $lines);

        $this->assertStringContainsString('CT1', $lines[0]->getComment());
        $this->assertStringContainsString('PARSED', $lines[0]->getCommand());
    }
}
