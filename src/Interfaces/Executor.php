<?php

declare(strict_types=1);

namespace CronManager\Interfaces;

interface Executor
{
    public function exec(string $command): ExecutorResult;
}
