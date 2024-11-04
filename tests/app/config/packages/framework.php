<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (FrameworkConfig $framework, ContainerConfigurator $container): void {
    $framework
        ->test(true)
        ->secret(env('APP_SECRET'))
        ->httpMethodOverride(false)
        ->handleAllThrowables(true)
        ->session(['storage_factory_id' => 'session.storage.factory.mock_file'])
    ;
    $framework->phpErrors(['log' => true]);
};
