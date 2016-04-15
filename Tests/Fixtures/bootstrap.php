<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

if (!is_file($loaderFile = __DIR__.'/../../vendor/autoload.php') && !is_file($loaderFile = __DIR__.'/../../../../../../../vendor/autoload.php')) {
    throw new LogicException('Could not find autoload.php in vendor/. Did you run "composer install --dev"?');
}

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require $loaderFile;
$loader->add('', __DIR__.'/src/');
$loader->register();

AnnotationRegistry::registerLoader([
    $loader,
    'loadClass'
]);

require_once __DIR__.'/app/AppKernel.php';
