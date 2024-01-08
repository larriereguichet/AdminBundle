<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Filter\Factory\FilterFactoryInterface;
use LAG\AdminBundle\Resource\Factory\EventOperationFactory;
use LAG\AdminBundle\Resource\Factory\EventResourceFactory;
use LAG\AdminBundle\Resource\Factory\OperationFactory;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Resource\Factory\PropertyFactory;
use LAG\AdminBundle\Resource\Factory\PropertyFactoryInterface;
use LAG\AdminBundle\Resource\Factory\ResourceFactory;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Resource\Locator\MetadataLocatorInterface;
use LAG\AdminBundle\Resource\Locator\ResourceLocatorInterface;
use LAG\AdminBundle\Resource\Resolver\ResourceResolver;
use LAG\AdminBundle\Resource\Resolver\ResourceResolverInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    // Resources factories
    $services->set(ResourceFactoryInterface::class, ResourceFactory::class)
        ->arg('$validator', service('validator'))
        ->arg('$propertyFactory', service(PropertyFactoryInterface::class))
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
};
