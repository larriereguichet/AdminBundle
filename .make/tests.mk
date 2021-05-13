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
	bin/php-cs-fixer fix --using-cache=no --diff --allow-risky=yes --config .php-cs-fixer.dist.php

# phpstan
phpstan.analyse:
	bin/phpstan analyse --level=1 src
	bin/phpstan analyse --level=1 tests/phpunit


# Misc
bc.check:
	bin/roave-backward-compatibility-check

tests.var-dump-checker:

tests.var-dump-checker.ci:
	bin/var-dump-check --symfony --exclude vendor --exclude demo .
