all: install

install:
	composer install
	make assets

update:
	composer update
	make assets

assets-build:
	php sam.php
