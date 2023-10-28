.PHONY: tests

tests: phpunit phpstan var-dump-checker cs

# PHPUnit
phpunit:
	$(PHP) bin/phpunit
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

phpunit.stop-on-failure:
	$(PHP) bin/phpunit --stop-on-failure
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

# PHPCSFixer
cs.fix:
	PHP_CS_FIXER_IGNORE_ENV=1 $(PHP) bin/php-cs-fixer fix --using-cache=no --diff --allow-risky=yes --config .php-cs-fixer.dist.php

cs:
	PHP_CS_FIXER_IGNORE_ENV=1 $(PHP) bin/php-cs-fixer fix --using-cache=no --diff --allow-risky=yes --config .php-cs-fixer.dist.php --dry-run

# phpstan
phpstan:
	$(PHP) bin/phpstan analyse --level=5 src

# Misc
bc.check:
	$(PHP) bin/roave-backward-compatibility-check

var-dump-checker:
	$(PHP) bin/var-dump-check --symfony src
	$(PHP) bin/var-dump-check --symfony tests
