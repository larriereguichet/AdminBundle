<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
//use Symfony\Component\HttpFoundation\Request;

if (!is_file($loaderFile = __DIR__.'/../vendor/autoload.php') && !is_file($loaderFile = __DIR__.'/../../../../../../vendor/autoload.php')) {
    throw new LogicException('Could not find autoload.php in vendor/. Did you run "composer install --dev"?');
}

$loader = require $loaderFile;

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

//$loader = require_once __DIR__.'/app/bootstrap.php.cache';
require_once __DIR__.'/app/AppKernel.php';
