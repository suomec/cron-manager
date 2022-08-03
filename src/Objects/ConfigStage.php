<?php

declare(strict_types=1);

namespace CronManager\Objects;

class ConfigStage
{
    private string $name;
    /** @var array<string, string> */
    private array $variables;

    /**
     * @param string $name
     * @param array<string, string> $variables
     */
    public function __construct(string $name, array $variables)
    {
        $this->name = $name;
        $this->variables = $variables;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, string>
     */
    public function getVariables(): array
    {
        return $this->variables;
    }
}
