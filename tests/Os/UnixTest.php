<?php

declare(strict_types=1);

namespace CronManager\Tests\Os;

use CronManager\Exceptions\OsException;
use CronManager\Interfaces\Executor;
use CronManager\Interfaces\ExecutorResult;
use CronManager\Os\Unix;
use CronManager\Tests\Core\TestCase;

class UnixTest extends TestCase
{
    public function testInstallerUnixGetSuccessIfNoCrontabForUser(): void
    {
        $exec = $this->createMock(Executor::class);
        $exec->method('exec')->willReturn(new ExecutorResult([], 0), new ExecutorResult([], 0));

        $unix = new Unix($exec);
        $result = $unix->getCrontab();

        $this->assertEquals([], $result);
    }

    public function testInstallerUnixGetFailsIfExitCodeNotZero(): void
    {
        $exec = $this->createMock(Executor::class);
        $exec->method('exec')->willReturn(new ExecutorResult([], 0), new ExecutorResult([], 100));

        $unix = new Unix($exec);

        $this->expectException(OsException::class);
        $this->expectExceptionMessage("command exited with non zero code: ");
        $unix->getCrontab();
    }

    public function testInstallerUnixGetSuccessForNotEmptyCrontab(): void
    {
        $exec = $this->createMock(Executor::class);
        $exec->method('exec')->willReturn(new ExecutorResult([], 0), new ExecutorResult([
            '123', '234', '345',
        ], 0));

        $unix = new Unix($exec);
        $result = $unix->getCrontab();

        $this->assertEquals(['123', '234', '345'], $result);
    }
}
