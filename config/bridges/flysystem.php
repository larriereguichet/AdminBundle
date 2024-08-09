<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Bridge\Flysystem\Registry\StorageRegistry;
use LAG\AdminBundle\Bridge\Flysystem\Registry\StorageRegistryInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Registry
    $services->set(StorageRegistryInterface::class, StorageRegistry::class)
        ->arg('$storages', tagged_iterator(tag: 'flysystem.storage', indexAttribute: 'storage'))
    ;
};
