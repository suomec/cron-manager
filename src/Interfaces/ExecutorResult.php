<?php

declare(strict_types=1);

namespace CronManager\Interfaces;

/**
 * Data from exec command of Executor
 */
class ExecutorResult
{
    /** @var string[] */
    private array $output;
    private int $code;

    /**
     * @param string[] $output
     * @param int $code
     */
    public function __construct(array $output, int $code)
    {
        $this->output = $output;
        $this->code = $code;
    }

    /**
     * @return string[]
     */
    public function getOutput(): array
    {
        return $this->output;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }
}
