<?php

declare(strict_types=1);

namespace CronManager\Installers;

use CronManager\Exceptions\InstallerException;
use CronManager\Interfaces\Executor;
use CronManager\Interfaces\Installer;

class Unix implements Installer
{
    private const COMMAND = '/usr/bin/crontab';
    private Executor $executor;

    public function __construct(Executor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * @throws InstallerException
     */
    public function get(): array
    {
        $result = $this->executor->exec(sprintf('%s -l 2>&1', self::COMMAND));

        $tmp = implode("\n", $result->output);
        if (strpos($tmp, 'no crontab for') !== false) {
            return [];
        }

        if ($result->code !== 0) {
            throw new InstallerException('command exited with non zero code: ' . $tmp);
        }

        return array_values($result->output);
    }

    /**
     * @throws InstallerException
     */
    public function set(array $lines): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'php-cron-generator');
        if (!is_string($tmp)) {
            throw new InstallerException("can't create tmp name");
        }

        file_put_contents($tmp, trim(implode("\n", $lines)) . "\n");

        $result = $this->executor->exec(sprintf('%s %s 2>&1', self::COMMAND, $tmp));

        @unlink($tmp);

        if ($result->code !== 0) {
            throw new InstallerException('command exited with non zero code: ' . implode("\n", $result->output));
        }
    }
}
