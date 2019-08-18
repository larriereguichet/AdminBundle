all: install

.PHONY: tests install update assets@build php-cs-fixer@fix php-cs-fixer@install

current_dir = $(shell pwd)

install:
	composer install
	make assets

update:
	composer update
	make assets

assets@build:
	php sam.php

### PHPUnit ###
tests: php-cs-fixer@fix
	bin/phpunit
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

### CsFixer ###
php-cs-fixer@fix:
	php-cs-fixer fix

php-cs-fixer@install:
	@echo "Install binary using composer (globally)"
	composer global require friendsofphp/php-cs-fixer
	@echo "Exporting composer binary path"
	@export PATH="$PATH:$HOME/.composer/vendor/bin"
