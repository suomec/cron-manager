<?php

declare(strict_types=1);

namespace CronManager\Tools;

use CronManager\Interfaces\Executor;
use CronManager\Interfaces\ExecutorResult;

class ExecutorReal implements Executor
{
    public function exec(string $command): ExecutorResult
    {
        exec($command, $out, $code);

        $result = new ExecutorResult();
        $result->output = $out;
        $result->code = $code;

        return $result;
    }
}
