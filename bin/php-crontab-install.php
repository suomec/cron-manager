<?php

error_reporting(-1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

$autoload = __DIR__ . '/../var/vendor/autoload.php';
if (!file_exists($autoload)) {
    echo "Can't find file: `{$autoload}` (composer.phar install)\n";
    exit(1);
}

require_once $autoload;

use CronManager\Integration;
use CronManager\Os\Unix;
use CronManager\Tools\ExecutorReal;

if (!isset($argv[1]) || !is_string($argv[1])) {
    echo "Pass first parameter: path to config file\n";
    exit(1);
}

if (!isset($argv[2]) || !is_string($argv[2])) {
    echo "Pass second parameter: stage name\n";
    exit(1);
}

try {
    (new Integration())->parseJSONConfigAndInstall(new Unix(new ExecutorReal()), $argv[1], $argv[2]);
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
    exit(1);
}

echo "Ok\n";
