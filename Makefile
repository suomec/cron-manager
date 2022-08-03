docker-start:
	docker-compose up -d

docker-session:
	docker exec -it suomec-cron-manager-php74 /bin/bash

check:
	./var/vendor/bin/php-cs-fixer --dry-run --using-cache=no --diff fix ./src
	./var/vendor/bin/php-cs-fixer --dry-run --using-cache=no --diff fix ./tests
	./var/vendor/bin/phpstan analyze --level max src tests bin

test:
	./var/vendor/bin/phpunit --bootstrap=./tests/Core/bootstrap.php --colors=always ./
