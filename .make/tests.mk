.PHONY: tests tests.ci tests.stop-on-failure php-cs-fixer.fix php-cs-fixer.ci phpstan.analyse phpunit.run tests.stop-on-failure bc.check tests.var-dump-checker.ci

tests: phpunit.run php-cs-fixer.fix phpstan.analyse tests.var-dump-checker

tests.stop-on-failure:
	bin/phpunit --stop-on-failure -v

# PHPUnit
phpunit.run:
	bin/phpunit
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

phpunit.run.stop-on-failure:
	bin/phpunit --stop-on-failure
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"


# PHPCSFixer
php-cs-fixer.fix:
	php-cs-fixer fix --using-cache=no --diff --allow-risky=yes

php-cs-fixer.install:
	@echo "Install binary using composer (globally)"
	composer global require friendsofphp/php-cs-fixer
	@echo "Exporting composer binary path"
	@export PATH="$PATH:$HOME/.composer/vendor/bin"


# phpstan
phpstan.analyse:
	bin/phpstan analyse --level=1 src
	bin/phpstan analyse --level=1 tests/AdminBundle


# Misc
bc.check:
	bin/roave-backward-compatibility-check

tests.var-dump-checker.ci:
	bin/var-dump-check --symfony --exclude vendor --exclude demo .

# Infection
infection.install:
	wget https://github.com/infection/infection/releases/download/0.21.0/infection.phar
	wget https://github.com/infection/infection/releases/download/0.21.0/infection.phar.asc
	gpg --recv-keys C6D76C329EBADE2FB9C458CFC5095986493B4AA0
	gpg --with-fingerprint --verify infection.phar.asc infection.phar
	rm infection.phar.asc
	sudo mv infection.phar /usr/local/bin/infection
	sudo chmod +x /usr/local/bin/infection

infection.run:
	infection --threads=4
