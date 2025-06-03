<?php

require dirname(__DIR__).'/vendor/autoload.php';

use LAG\AdminBundle\Tests\Application\TestKernel;

if (!is_file(dirname(__DIR__).'/vendor/autoload_runtime.php')) {
    throw new LogicException('Symfony Runtime is missing. Try running "composer require symfony/runtime".');
}

$kernel = new TestKernel('test', true);
new Symfony\Component\Filesystem\Filesystem()->remove($kernel->getCacheDir());
