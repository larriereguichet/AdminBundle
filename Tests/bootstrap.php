<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
//use Symfony\Component\HttpFoundation\Request;

if (!is_file($loaderFile = __DIR__.'/../vendor/autoload.php') && !is_file($loaderFile = __DIR__.'/../../../../../../vendor/autoload.php')) {
    throw new LogicException('Could not find autoload.php in vendor/. Did you run "composer install --dev"?');
}

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require $loaderFile;
$loader->add('', __DIR__ . '/src/');
$loader->register();



//require_once __DIR__ . '/src/Entity/TestEntity.php';
//$classLoader = new \Doctrine\Common\ClassLoader('Entity', __DIR__ . '/src');
//$classLoader->registe();

//$loader = new \Symfony\Component\ClassLoader\UniversalClassLoader();
//$loader->registerNamespace('TestBundle', __DIR__ . '/src');
//$loader->register();

//var_dump($classLoader->getIncludePath());
//print_r(get_declared_classes());
//die;
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
//require_once __DIR__ . '/src/Test/TestBundle/TestTestBundle.php';
//AnnotationRegistry::registerAutoloadNamespace('TestBundle', __DIR__ . '/src/');
//$loader = require_once __DIR__.'/app/bootstrap.php.cache';

//require_once __DIR__ . '/src/Test/TestBundle/TestTestBundle.php';
require_once __DIR__.'/app/AppKernel.php';



