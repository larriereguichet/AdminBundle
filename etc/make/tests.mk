.PHONY: tests tests.ci tests.stop-on-failure php-cs-fixer.fix php-cs-fixer.ci phpstan.analyse phpunit.run security.check tests.stop-on-failure bc.check

# PHPUnit
tests: phpunit.run php-cs-fixer.fix phpstan.analyse security.check tests.var-dump-checker

tests.ci: phpunit.run php-cs-fixer.ci phpstan.analyse security.check tests.var-dump-checker

tests.stop-on-failure:
	bin/phpunit --stop-on-failure -v

phpunit.run:
	bin/phpunit
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

phpunit.run.stop-on-failure:
	bin/phpunit --stop-on-failure
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

# CodeStyle
php-cs-fixer.fix:
	php-cs-fixer fix

php-cs-fixer.ci:
	php php-cs-fixer fix --dry-run --using-cache=no --verbose

php-cs-fixer.install:
	@echo "Install binary using composer (globally)"
	composer global require friendsofphp/php-cs-fixer
	@echo "Exporting composer binary path"
	@export PATH="$PATH:$HOME/.composer/vendor/bin"

phpstan.analyse:
	bin/phpstan analyse --level=1 src
	bin/phpstan analyse --level=1 tests

# Misc
security.check:
	bin/security-checker security:check

bc.check:
	bin/roave-backward-compatibility-check

tests.var-dump-checker:
	bin/var-dump-check --symfony --exclude vendor .
