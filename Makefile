current_dir = $(shell pwd)
DOCKER_COMPOSE=docker compose
JS = $(DOCKER_COMPOSE) run --rm js

.PHONY: tests phpunit phpstan rector var-dump-checker cs

tests: phpunit phpstan rector var-dump-checker cs

# PHP
phpunit:
	XDEBUG_MODE=coverage vendor/bin/phpunit
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

phpunit.stop-on-failure:
	vendor/bin/phpunit --stop-on-failure
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

cs.fix:
	PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix --diff --allow-risky=yes --config .php-cs-fixer.dist.php

cs:
	PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix --diff --allow-risky=yes --config .php-cs-fixer.dist.php --dry-run

phpstan:
	vendor/bin/phpstan analyse

rector:
	vendor/bin/rector --dry-run

rector.fix:
	vendor/bin/rector

bc.check:
	vendor/bin/roave-backward-compatibility-check

var-dump-checker:
	vendor/bin/var-dump-check --symfony src
	vendor/bin/var-dump-check --symfony tests

# Assets
.PHONY: assets assets.production assets.build assets.watch assets.install

assets: assets.install assets.production

assets.dev:
	$(JS) yarn run encore dev

assets.watch:
	$(JS) yarn run encore dev --watch

assets.production:
	$(JS) yarn run encore production

assets.install:
	$(JS) yarn install
