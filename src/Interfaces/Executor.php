<?php

declare(strict_types=1);

namespace CronManager\Interfaces;

interface Executor
{
    /**
     * Executes command on OS
     * @param string $command Command such as `sleep 10 && reboot`
     * @return ExecutorResult Result of command execution
     */
    public function exec(string $command): ExecutorResult;
}
