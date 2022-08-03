<?php

declare(strict_types=1);

namespace CronManager\Integrations;

use CronManager\Interfaces\Installer as OsInstaller;
use CronManager\Objects\Crontab;

class Installer
{
    public static function install(Crontab $crontab, OsInstaller $os): void
    {
        // search for and old installation
        $iStart = $iFinish = 0;
        $foundStart = $foundFinish = false;

        $hash = md5($crontab->getKey());
        $oldLines = $os->get();

        foreach ($oldLines as $idx => $line) {
            if (strpos($line, $hash) === false) {
                continue;
            }

            if (!$foundStart) {
                $foundStart = true;
                $iStart = $idx;

                continue;
            }

            $foundFinish = true;
            $iFinish = $idx;

            break;
        }

        $newLines = array_merge(
            [sprintf("### %s (%s) DO NOT EDIT", $crontab->getName(), $hash)],
            $crontab->generateLines(),
            [sprintf("### FINISH %s DO NOT EDIT", $hash)],
        );

        if ($foundStart && $foundFinish) {
            // replace between start and finish indexes
            array_splice($oldLines, $iStart, ($iFinish - $iStart) + 1, $newLines);

            $toInstall = $oldLines;
        } else {
            // insert after old
            $toInstall = array_merge(
                $oldLines,
                $newLines,
            );
        }

        $os->set($toInstall);
    }
}
