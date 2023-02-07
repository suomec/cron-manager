# PHP crontab generator from friendly JSON config

Allows you to set up crontab on unix systems from JSON-config. Every cronjob in config can be enabled or disabled,
bound to list of stages and has human-readable schedule format, for example:

* Every day at 10:10 → `10 10 * * *`
* Every hour at 10th minute → `10 * * * *`
* Every minute → `* * * * *`
* Every 6 minutes → `*/6 * * * *`
* Every 6 hours → `0 */6 * * *`
* Every 3 days → `0 0 */3 * *`
* Or raw expression `raw:* * */2 */2 *` → `* * */2 */2 *`

# Example of config (/var/examples/config.json)

```json
{
  "name": "Cron tasks for some project",
  "key": "unique key (should not be changed after was set)",
  "stages": [
    {
      "name": "production",
      "variables": {
        "argument": "production-argument",
        "base_dir": "/var/www/production"
      }
    },
    {
      "name": "testing",
      "variables": {
        "argument": "testing-argument",
        "base_dir": "/var/www/testing"
      }
    }
  ],
  "tasks": [
    {
      "name": "first task",
      "is_enabled" : true,
      "stages": ["production", "testing"],
      "schedule": "every 15 minutes",
      "command": "cd {base_dir} && ./run/command {argument}"
    },
    {
      "name": "second task",
      "is_enabled": false,
      "stages": ["testing"],
      "schedule": "every 10 minutes",
      "command": "cd {base_dir} && ./run/command {argument}"
    }
  ]
}
```

If you run command ```cron-manager.phar /path/to/config testing```. Where `testing` is stage name from first 
part of config next crontab will be installed:

```text
### Cron tasks for some project (f96162a86...HASH-FROM-UNIQUE-KEY) DO NOT EDIT
# first task
*/15 * * * * cd /var/www/testing && ./run/command testing-argument
### FINISH f96162a86...HASH-FROM-UNIQUE-KEY DO NOT EDIT
```

Hash ``f96162a86...`` is the md5() from `key`. Second command is disabled.

# Schedule formats

Located at /src/Parsers/*Parser.php

# Usage

Download release and run `cron-manager.phar` with two arguments:
* Path to JSON config
* Stage name (described in `stages` section)

Or run `./bin/php-crontab-install` with two same arguments.
