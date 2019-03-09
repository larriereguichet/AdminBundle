all: install

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
test@phpunit:
	bin/phpunit
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

### CsFixer ###
php-cs-fixer@fix:
	bin/php-cs-fixer fix
