.PHONY: tests php-cs-fixer.fix phpstan.analyse phpunit.run security.check tests.stop-on-failure

### PHPUnit ###
tests: phpunit.run php-cs-fixer.fix phpstan.analyse security.check

tests.stop-on-failure:
	bin/phpunit --stop-on-failure -v

phpunit.run:
	bin/phpunit
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

phpunit.run.stop-on-failure:
	bin/phpunit --stop-on-failure
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

### CodeStyle ###
php-cs-fixer.fix:
	php-cs-fixer fix

php-cs-fixer.install:
	@echo "Install binary using composer (globally)"
	composer global require friendsofphp/php-cs-fixer
	@echo "Exporting composer binary path"
	@export PATH="$PATH:$HOME/.composer/vendor/bin"

phpstan.analyse:
	bin/phpstan analyse --level=1 src
	bin/phpstan analyse --level=1 tests
##################

security.check:
	bin/security-checker security:check
