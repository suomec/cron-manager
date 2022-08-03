<?php

declare(strict_types=1);

namespace CronManager\Interfaces;

class ExecutorResult
{
    /** @var string[] */
    public array $output;
    public int $code;
}
