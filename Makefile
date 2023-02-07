docker-start:
	docker-compose up -d

docker-session:
	docker exec -it suomec-cron-manager-php74 /bin/bash

docker-rebuild:
	docker-compose up -d --build suomec-cron-manager-php74

check:
	./var/vendor/bin/php-cs-fixer --dry-run --using-cache=no --diff fix ./src
	./var/vendor/bin/php-cs-fixer --dry-run --using-cache=no --diff fix ./tests
	./var/vendor/bin/phpstan analyze --level max src tests bin

test:
	./var/vendor/bin/phpunit --bootstrap=./tests/Core/bootstrap.php --colors=always ./

filter-test:
	./var/vendor/bin/phpunit --bootstrap=./tests/Core/bootstrap.php --colors=always ./  --filter "$(FILTER)"

phar:
	php composer.phar update -q && php composer.phar install --no-dev -q
	./bin/php-crontab-make-phar
	php composer.phar install -q
