all: install

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

### CsFixer ###
php-cs-fixer@fix:
	bin/php-cs-fixer fix
