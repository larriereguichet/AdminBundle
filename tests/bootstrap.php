<?php

require dirname(__DIR__).'/vendor/autoload.php';

use LAG\AdminBundle\Tests\Application\TestKernel;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;

if (!is_file(dirname(__DIR__).'/vendor/autoload_runtime.php')) {
    throw new LogicException('Symfony Runtime is missing. Try running "composer require symfony/runtime".');
}

$kernel = new TestKernel('test', true);
(new Symfony\Component\Filesystem\Filesystem())->remove($kernel->getCacheDir());

$application = new Application($kernel);
$application->setAutoExit(false);

$input = new ArrayInput(['command' => 'doctrine:database:drop', '--no-interaction' => true, '--force' => true, '--quiet' => true]);
$application->run($input, new ConsoleOutput());

$input = new ArrayInput(['command' => 'doctrine:database:create', '--no-interaction' => true]);
$application->run($input, new ConsoleOutput());

$input = new ArrayInput(['command' => 'doctrine:schema:create']);
$application->run($input, new ConsoleOutput());

$input = new ArrayInput(['command' => 'cache:clear']);
$application->run($input, new ConsoleOutput());

$finder = (new \Symfony\Component\Finder\Finder())
    ->in(dirname(__DIR__).'/public')
    ->files()
;

foreach ($finder as $file) {
    (new Symfony\Component\Filesystem\Filesystem())
        ->copy($file->getRealPath(), __DIR__.'/app/public/build/'.$file->getRelativePathname());
}

