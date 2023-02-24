.PHONY: tests

tests: php-cs-fixer.run phpstan.run var-dump-checker.run phpunit.run

# PHPUnit
phpunit.run:
	$(PHP) bin/phpunit
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

phpunit.stop-on-failure:
	$(PHP) bin/phpunit --stop-on-failure
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

# PHPCSFixer
php-cs-fixer.fix:
	PHP_CS_FIXER_IGNORE_ENV=1 $(PHP) bin/php-cs-fixer fix --using-cache=no --diff --allow-risky=yes --config .php-cs-fixer.dist.php

php-cs-fixer.run:
	PHP_CS_FIXER_IGNORE_ENV=1 $(PHP) bin/php-cs-fixer fix --using-cache=no --diff --allow-risky=yes --config .php-cs-fixer.dist.php --dry-run

# phpstan
phpstan.run:
	$(PHP) bin/phpstan analyse --level=5 src

# Misc
bc.check:
	$(PHP) bin/roave-backward-compatibility-check

var-dump-checker.run:
	$(PHP) bin/var-dump-check --symfony src
	$(PHP) bin/var-dump-check --symfony tests
