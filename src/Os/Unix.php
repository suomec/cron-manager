<?php

declare(strict_types=1);

namespace CronManager\Os;

use CronManager\Exceptions\OsException;
use CronManager\Interfaces\Executor;
use CronManager\Interfaces\Os;

class Unix implements Os
{
    private const COMMAND = '/usr/bin/crontab';

    private Executor $executor;

    public function __construct(Executor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * @throws OsException
     */
    public function readFile(string $path): string
    {
        if (!file_exists($path)) {
            throw new OsException("file not found: {$path}");
        }

        $contents = file_get_contents($path);
        if (!is_string($contents)) {
            throw new OsException("can't read file: {$path}");
        }

        return $contents;
    }

    /**
     * @throws OsException
     */
    public function getCrontab(): array
    {
        $this->isCrontabExists();

        $result = $this->executor->exec(sprintf('%s -l 2>&1', self::COMMAND));

        $tmp = implode("\n", $result->getOutput());
        if (strpos($tmp, 'no crontab for') !== false) {
            return [];
        }

        if ($result->getCode() !== 0) {
            throw new OsException('command exited with non zero code: ' . $tmp);
        }

        return array_values($result->getOutput());
    }

    /**
     * @throws OsException
     */
    public function setCrontab(array $lines): void
    {
        $this->isCrontabExists();

        $tmp = tempnam(sys_get_temp_dir(), 'php-cron-generator');
        if (!is_string($tmp)) {
            throw new OsException("can't create tmp name");
        }

        file_put_contents($tmp, trim(implode("\n", $lines)) . "\n");

        $result = $this->executor->exec(sprintf('%s %s 2>&1', self::COMMAND, $tmp));

        @unlink($tmp);

        if ($result->getCode() !== 0) {
            throw new OsException('command exited with non zero code: ' . implode("\n", $result->getOutput()));
        }
    }

    /**
     * Fails if crontab command not found
     * @return void
     */
    private function isCrontabExists(): void
    {
        $result = $this->executor->exec(sprintf("command -v %s", self::COMMAND));

        if ($result->getCode() !== 0) {
            throw new OsException("crontab command is not accessible");
        }
    }
}
