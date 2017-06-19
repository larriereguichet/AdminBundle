all: install

install:
	composer install
	make assets-build

update:
	composer update
	make assets

assets-build:
	bin/sam jk:assets:build --config=src/LAG/AdminBundle/Resources/config/assets.yml -vvv
