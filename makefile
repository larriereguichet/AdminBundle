all:install

install:
	composer install
	make assets	

assets:
	compass compile
