<?php

declare(strict_types=1);

namespace CronManager\Objects;

class Crontab
{
    private string $name;
    private string $key;
    /** @var CrontabLine[] */
    private array $lines;

    /**
     * @param string $name
     * @param string $key
     * @param CrontabLine[] $lines
     */
    public function __construct(string $name, string $key, array $lines)
    {
        $this->name = $name;
        $this->key = $key;
        $this->lines = $lines;
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
     * @return CrontabLine[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * @return string[]
     */
    public function generateLines(): array
    {
        $lines = [];

        foreach ($this->lines as $line) {
            $lines[] = sprintf('# %s', $line->getComment());
            $lines[] = $line->getCommand();
        }

        return $lines;
    }
}
