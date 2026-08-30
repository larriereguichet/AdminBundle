<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Resource\Slug\ResourceSlugger;
use LAG\AdminBundle\Resource\Slug\ResourceSluggerInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Slugger
    $services->set(ResourceSluggerInterface::class, ResourceSlugger::class)
        ->args([
            '$slugger' => service('slugger'),
            '$propertyAccessor' => service('property_accessor'),
        ])
        ->alias('lag_admin.slugger', ResourceSluggerInterface::class)
    ;
};
