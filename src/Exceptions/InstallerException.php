<?php

declare(strict_types=1);

namespace CronManager\Exceptions;

class InstallerException extends \Exception
{
    //@phpstan-ignore-next-line
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
