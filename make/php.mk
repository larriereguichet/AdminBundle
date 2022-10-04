.PHONY: tests

tests: phpunit.run php-cs-fixer.fix phpstan.analyse bc.check var-dump-checker.run

tests.stop-on-failure: phpunit.stop-on-failure php-cs-fixer.fix phpstan.analyse bc.check var-dump-checker.run

# PHPUnit
phpunit.run:
	$(PHP) bin/phpunit
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

phpunit.stop-on-failure:
	$(PHP) bin/phpunit --stop-on-failure
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

# PHPCSFixer
php-cs-fixer.fix:
	$(PHP) bin/php-cs-fixer fix --using-cache=no --diff --allow-risky=yes --config .php-cs-fixer.dist.php

# phpstan
phpstan.analyse:
	$(PHP) bin/phpstan analyse --level=5 src

# Misc
bc.check:
	$(PHP) bin/roave-backward-compatibility-check

var-dump-checker.run:
	$(PHP) bin/var-dump-check --symfony src
	$(PHP) bin/var-dump-check --symfony tests