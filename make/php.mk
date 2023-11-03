.PHONY: tests

tests: phpunit phpstan var-dump-checker cs

# PHPUnit
phpunit:
	$(PHP) ./vendor/bin/phpunit
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

phpunit.stop-on-failure:
	$(PHP) ./vendor/bin/phpunit --stop-on-failure
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

# PHPCSFixer
cs.fix:
	PHP_CS_FIXER_IGNORE_ENV=1 $(PHP) ./vendor/bin/php-cs-fixer fix --diff --allow-risky=yes --config .php-cs-fixer.dist.php

cs:
	PHP_CS_FIXER_IGNORE_ENV=1 $(PHP) ./vendor/bin/php-cs-fixer fix --diff --allow-risky=yes --config .php-cs-fixer.dist.php --dry-run

# phpstan
phpstan:
	$(PHP) ./vendor/bin/phpstan analyse --level=5 src config

# Misc
bc.check:
	$(PHP) ./vendor/bin/roave-backward-compatibility-check

var-dump-checker:
	$(PHP) ./vendor/bin/var-dump-check --symfony src
	$(PHP) ./vendor/bin/var-dump-check --symfony tests
