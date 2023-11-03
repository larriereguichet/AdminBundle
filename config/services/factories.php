<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Event\Dispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Filter\Factory\FilterFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\EventOperationFactory;
use LAG\AdminBundle\Metadata\Factory\OperationFactory;
use LAG\AdminBundle\Metadata\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\PropertyFactory;
use LAG\AdminBundle\Metadata\Factory\PropertyFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\ResourceFactory;
use LAG\AdminBundle\Metadata\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\EventResourceFactory;
use LAG\AdminBundle\Metadata\Factory\ResourceResolver;
use LAG\AdminBundle\Metadata\Factory\ResourceResolverInterface;
use LAG\AdminBundle\Metadata\Locator\MetadataLocatorInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    // Resources factories
    $services->set(ResourceFactoryInterface::class, ResourceFactory::class)
        ->arg('$validator', service('validator'))
        ->arg('$operationFactory', service(OperationFactoryInterface::class))
    ;
    $services->set(EventResourceFactory::class)
        ->decorate(ResourceFactoryInterface::class)
        ->arg('$validator', service('validator'))
    ;
    $services->set(EventResourceFactory::class)
        ->decorate(ResourceFactoryInterface::class, priority: 255)
        ->arg('$eventDispatcher', service(ResourceEventDispatcherInterface::class))
        ->arg('$resourceFactory', service('.inner'))
    ;

    // Operations factories
    $services->set(OperationFactoryInterface::class, OperationFactory::class)
        ->arg('$propertyFactory', service(PropertyFactoryInterface::class))
        ->arg('$filterFactory', service(FilterFactoryInterface::class))
    ;
    $services->set(EventOperationFactory::class)
        ->decorate(OperationFactoryInterface::class)
        ->arg('$eventDispatcher', service(ResourceEventDispatcherInterface::class))
        ->arg('$operationFactory', service('.inner'))
    ;

    // Properties factories
    $services->set(PropertyFactoryInterface::class, PropertyFactory::class)
        ->arg('$validator', service('validator'))
    ;

    // Resolvers
    $services->set(ResourceResolverInterface::class, ResourceResolver::class)
        ->arg('$resourcePaths', param('lag_admin.resource_paths'))
        ->arg('$locator', service(MetadataLocatorInterface::class))
        ->arg('$resourceFactory', service(ResourceFactoryInterface::class))
        ->alias('lag_admin.resource.resolver', ResourceResolverInterface::class)
        ->public()
    ;
};
