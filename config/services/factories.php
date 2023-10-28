<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Filter\Factory\FilterFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\OperationFactory;
use LAG\AdminBundle\Metadata\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\PropertyFactory;
use LAG\AdminBundle\Metadata\Factory\PropertyFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\ResourceFactory;
use LAG\AdminBundle\Metadata\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\ResourceFactoryValidationDecorator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    // Resources factories
    $services->set(ResourceFactoryInterface::class, ResourceFactory::class)
        ->arg('$eventDispatcher', service('event_dispatcher'))
        ->arg('$operationFactory', service(OperationFactoryInterface::class))
    ;

    $services->set(ResourceFactoryValidationDecorator::class)
        ->decorate(ResourceFactoryInterface::class)
        ->arg('$validator', service('validator'))
        ->arg('$decorated', service('.inner'))
    ;

    // Operations factories
    $services->set(OperationFactoryInterface::class, OperationFactory::class)
        ->arg('$eventDispatcher', service('event_dispatcher'))
        ->arg('$propertyFactory', service(PropertyFactoryInterface::class))
        ->arg('$filterFactory', service(FilterFactoryInterface::class))
    ;

    // Properties factories
    $services->set(PropertyFactoryInterface::class, PropertyFactory::class)
        ->arg('$validator', service('validator'))
    ;
};
