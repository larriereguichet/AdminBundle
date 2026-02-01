<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Metadata\Factory\GridMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\GridMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\GridProviderMetadataFactory;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(GridMetadataFactoryInterface::class, GridMetadataFactory::class);
    $services->set(GridProviderMetadataFactory::class)
        ->decorate(GridMetadataFactoryInterface::class)
    ;
};
