<?php

declare(strict_types=1);

namespace CronManager\Objects;

class CrontabLine
{
    private string $comment;
    private string $command;

    public function __construct(string $comment, string $command)
    {
        $this->comment = $comment;
        $this->command = $command;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getCommand(): string
    {
        return $this->command;
    }
}
