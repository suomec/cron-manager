<?php

declare(strict_types=1);

namespace CronManager\Parsers;

/**
 * Template: `raw:* /2 3 * 2000`
 */
class RawParser extends Base
{
    public function parse(string $raw): ?string
    {
        if (substr($raw, 0, 4) === 'raw:') {
            return substr($raw, 4);
        }

        return null;
    }
}
