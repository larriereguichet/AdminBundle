all: install

install:
	composer install
	make assets

update:
	composer update
	make assets

assets-build:
	php sam jk:assets:build --config=src/LAG/AdminBundle/Resources/config/assets.yml
