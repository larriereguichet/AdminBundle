<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener\InitializeResourceListener;
use LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener\OperationCreateListener;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelper;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataPropertyFactory;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataPropertyFactoryInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\DoctrineCollectionTransformProvider;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\ORMProvider;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\PaginationProvider;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\QueryBuilderProvider;
use LAG\AdminBundle\Filter\Factory\FilterFactoryInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // State providers
    $services->set(ORMProvider::class)
        ->arg('$registry', service('doctrine'))
        ->tag('lag_admin.data_provider', ['identifier' => 'doctrine', 'priority' => 0])
    ;
    $services->set(PaginationProvider::class)
        ->decorate(ProviderInterface::class, priority: 255)
        ->arg('$provider', service('.inner'))
    ;
    $services->set(QueryBuilderProvider::class)
        ->decorate(ProviderInterface::class, priority: 200)
        ->arg('$provider', service('.inner'))
    ;
    $services->set(DoctrineCollectionTransformProvider::class)
        ->decorate(ProviderInterface::class, priority: -200)
        ->arg('$provider', service('.inner'))
        ->arg('$serializer', service('serializer'))
    ;

    // State processors
    $services->set(ORMProcessor::class)
        ->arg('$registry', service('doctrine'))
        ->tag('lag_admin.data_processor', ['identifier' => 'doctrine', 'priority' => 0])
    ;

    // Event listeners
    $services->set(InitializeResourceListener::class)
        ->arg('$propertyFactory', service(MetadataPropertyFactoryInterface::class))
        ->tag('kernel.event_listener', [
            'event' => 'lag_admin.resource.create',
            'priority' => 200,
        ])
    ;
    $services->set(OperationCreateListener::class)
        ->arg('$filterFactory', service(FilterFactoryInterface::class))
        ->tag('kernel.event_listener', ['event' => 'lag_admin.operation.create', 'priority' => 255])
    ;

    // Doctrine metadata helpers
    $services->set(MetadataPropertyFactoryInterface::class, MetadataPropertyFactory::class)
        ->arg('$metadataHelper', service(MetadataHelperInterface::class))
    ;
    $services->set(MetadataHelperInterface::class, MetadataHelper::class)
        ->arg('$entityManager', service('doctrine.orm.entity_manager'))
    ;
};
