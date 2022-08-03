<?php

declare(strict_types=1);

namespace CronManager\Objects;

class ConfigTask
{
    private string $name;
    private bool $isEnabled;
    /** @var string[] */
    private array $stagesNames;
    private string $schedule;
    private string $command;

    /**
     * @param string $name
     * @param bool $isEnabled
     * @param string[] $stagesNames
     * @param string $schedule
     * @param string $command
     */
    public function __construct(string $name, bool $isEnabled, array $stagesNames, string $schedule, string $command)
    {
        $this->name = $name;
        $this->isEnabled = $isEnabled;
        $this->stagesNames = $stagesNames;
        $this->schedule = $schedule;
        $this->command = $command;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @return string[]
     */
    public function getStagesNames(): array
    {
        return $this->stagesNames;
    }

    public function getSchedule(): string
    {
        return $this->schedule;
    }

    public function getCommand(): string
    {
        return $this->command;
    }
}
