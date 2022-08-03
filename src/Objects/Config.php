<?php

declare(strict_types=1);

namespace CronManager\Objects;

class Config
{
    private string $name;
    private string $key;
    /** @var ConfigStage[] */
    private array $stages;
    /** @var ConfigTask[] */
    private array $tasks;

    /**
     * @param string $name
     * @param ConfigStage[] $stages
     * @param ConfigTask[] $tasks
     */
    public function __construct(string $name, string $key, array $stages, array $tasks)
    {
        $this->name = $name;
        $this->key = $key;
        $this->stages = $stages;
        $this->tasks = $tasks;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return ConfigStage[]
     */
    public function getStages(): array
    {
        return $this->stages;
    }

    /**
     * @return ConfigTask[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }
}
