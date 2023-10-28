<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener\OperationCreateListener;
use LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener\ResourceCreateListener;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelper;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataPropertyFactory;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataPropertyFactoryInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProvider;
use LAG\AdminBundle\Filter\Factory\FilterFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\PropertyFactoryInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(ORMDataProvider::class)
        ->arg('$registry', service('doctrine'))
        ->tag('lag_admin.data_provider', [
            'identifier' => 'doctrine',
            'priority' => 0,
        ])
    ;

    $services->set(ORMDataProcessor::class)
        ->arg('$registry', service('doctrine'))
        ->tag('lag_admin.data_processor', [
            'identifier' => 'doctrine',
            'priority' => 0,
        ])
    ;

    $services->set(ResourceCreateListener::class)
        ->arg('$propertyFactory', service(PropertyFactoryInterface::class))
        ->tag('kernel.event_listener', [
            'event' => 'lag_admin.resource.create',
            'priority' => 200,
        ])
    ;

    $services->set(OperationCreateListener::class)
        ->arg('$filterFactory', service(FilterFactoryInterface::class))
        ->tag('kernel.event_listener', [
            'event' => 'lag_admin.operation.create',
            'priority' => 256,
        ])
    ;

    $services->set(MetadataPropertyFactoryInterface::class, MetadataPropertyFactory::class)
        ->arg('$metadataHelper', service(MetadataHelperInterface::class))
    ;

    $services->set(MetadataHelperInterface::class, MetadataHelper::class)
        ->arg('$entityManager', service('doctrine.orm.entity_manager'))
    ;
};
