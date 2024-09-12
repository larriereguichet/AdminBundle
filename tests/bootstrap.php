<?php

require dirname(__DIR__).'/vendor/autoload.php';

// Temporary fix for the phpunit error message "Test code or tested code did not remove its own exception handlers"
// @see https://github.com/symfony/symfony/issues/53812
\Symfony\Component\ErrorHandler\ErrorHandler::register(null, false);
