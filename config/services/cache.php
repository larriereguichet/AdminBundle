<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Cache\Resource\Factory\CacheResourceFactory;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(CacheResourceFactory::class)
        ->args([
            '$metadataFactory' => service('.inner'),
            '$cache' => service('lag_admin.cache'),
        ])
        ->decorate('lag_admin.resource.factory', priority: 255)
    ;
};
