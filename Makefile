all: install

.PHONY: tests install update assets@build security@check phpunit@run

current_dir = $(shell pwd)

install:
	composer install
	make assets

update:
	composer update
	make assets

assets@build:
	php sam.php

security@check:
	bin/security-checker security:check

### PHPUnit ###
tests: php-cs-fixer@fix phpstan@analyse security@check phpunit@run

phpunit@run:
	bin/phpunit
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

### CodeStyle ###
.PHONY: php-cs-fixer@fix php-cs-fixer@install phpstan@analyse

php-cs-fixer@fix:
	php-cs-fixer fix

php-cs-fixer@install:
	@echo "Install binary using composer (globally)"
	composer global require friendsofphp/php-cs-fixer
	@echo "Exporting composer binary path"
	@export PATH="$PATH:$HOME/.composer/vendor/bin"

phpstan@analyse: composer.lock
	bin/phpstan analyse --level=1 src
	bin/phpstan analyse --level=1 tests
##################
