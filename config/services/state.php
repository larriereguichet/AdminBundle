<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Event\Dispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\State\Processor\CompositeDataProcessor;
use LAG\AdminBundle\State\Processor\DataProcessorInterface;
use LAG\AdminBundle\State\Provider\CompositeDataProvider;
use LAG\AdminBundle\State\Provider\DataProviderInterface;
use LAG\AdminBundle\State\Provider\JsonDataProviderDecorator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Data providers
    $services->set(DataProviderInterface::class, CompositeDataProvider::class)
        ->arg('$providers', tagged_iterator('lag_admin.data_provider'))
    ;
    $services->set(JsonDataProviderDecorator::class)
        ->decorate(DataProviderInterface::class)
        ->arg('$decorated', service('.inner'))
        ->arg('$serializer', service('serializer'))
    ;

    // Data processors
    $services->set(DataProcessorInterface::class, CompositeDataProcessor::class)
        ->arg('$processors', tagged_iterator('lag_admin.data_processor'))
        ->arg('$eventDispatcher', service(ResourceEventDispatcherInterface::class))
    ;
};
