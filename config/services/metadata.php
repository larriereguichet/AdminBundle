<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Metadata\Factory\ApplicationMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\ApplicationMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\CollectionOperationMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\GridCollectionAttributeMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\GridCollectionMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\GridCollectionMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\GridMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\GridMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\GridProviderMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\OperationIdentifiersMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\OperationRoutingMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\PropertyCollectionClassMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\PropertyCollectionMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\PropertyCollectionMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\PropertyMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\PropertyMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\ResourceCollectionAttributeMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\ResourceCollectionMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\ResourceCollectionMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\ResourceDefaultMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\ResourceMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\ResourceMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\ResourceOperationLinksMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\ResourcePropertiesMetadataFactory;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Application factories
    $services->set(ApplicationMetadataFactoryInterface::class, ApplicationMetadataFactory::class)
        ->args([
            '$applications' => param('lag_admin.applications'),
        ])
        ->alias('lag_admin.application.metadata_factory', ApplicationMetadataFactoryInterface::class)
    ;

    // Resource collection factories
    $services->set(ResourceCollectionMetadataFactoryInterface::class, ResourceCollectionMetadataFactory::class)
        ->alias('lag_admin.resource.collection_metadata_factory', ResourceCollectionMetadataFactoryInterface::class)
    ;
    $services->set(ResourceCollectionAttributeMetadataFactory::class)
        ->decorate('lag_admin.resource.collection_metadata_factory')
        ->args([
            '$metadataFactory' => service('.inner'),
            '$paths' => service('lag_admin.mapping_paths'),
        ])
    ;

    // Resource factories
    $services->set(ResourceMetadataFactoryInterface::class, ResourceMetadataFactory::class)
        ->args([
            '$resources' => param('lag_admin.resources'),
        ])
        ->alias('lag_admin.resource.metadata_factory', ResourceMetadataFactoryInterface::class)
    ;
    $services->set(ResourceDefaultMetadataFactory::class)
        ->decorate('lag_admin.resource.metadata_factory', priority: 255)
        ->args([
            '$metadataFactory' => service('.inner'),
            '$applicationFactory' => service('lag_admin.application.metadata_factory'),
        ])
    ;
    $services->set(ResourcePropertiesMetadataFactory::class)
        ->decorate('lag_admin.resource.metadata_factory', priority: 250)
        ->args([
            '$metadataFactory' => service('.inner'),
        ])
    ;
    $services->set(ResourceOperationLinksMetadataFactory::class)
        ->decorate('lag_admin.resource.metadata_factory', priority: -255)
        ->args([
            '$metadataFactory' => service('.inner'),
            '$linkMetadataFactory' => service('lag_admin.link.metadata_factory'),
        ])
    ;

    // Operation factories
    $services->set(OperationIdentifiersMetadataFactory::class)
        ->decorate('lag_admin.operation.metadata_factory', priority: 255)
        ->args([
            '$routeNameGenerator' => service(RouteNameGeneratorInterface::class),
        ])
    ;
    $services->set(OperationRoutingMetadataFactory::class)
        ->decorate('lag_admin.operation.metadata_factory', priority: 200)
        ->args([
            '$metadataFactory' => service('.inner'),
            '$routeNameGenerator' => service(RouteNameGeneratorInterface::class),
        ])
    ;
    $services->set(CollectionOperationMetadataFactory::class)
        ->decorate('lag_admin.operation.metadata_factory', priority: 250)
        ->args([
            '$metadataFactory' => service('.inner'),
        ])
    ;

    // Properties factories
    $services->set(PropertyMetadataFactoryInterface::class, PropertyMetadataFactory::class)
        ->alias('lag_admin.property.metadata_factory', PropertyMetadataFactoryInterface::class)
    ;
    $services->set(PropertyCollectionMetadataFactoryInterface::class, PropertyCollectionMetadataFactory::class)
        ->alias('lag_admin.property.collection_metadata_factory', PropertyCollectionMetadataFactoryInterface::class)
    ;
    $services->set(PropertyCollectionClassMetadataFactory::class)
        ->decorate('lag_admin.property.collection_metadata_factory', priority: -255)
    ;

    // Grid factories
    $services->set(GridCollectionMetadataFactoryInterface::class, GridCollectionMetadataFactory::class)
        ->args([
            '$paths' => service('lag_admin.mapping_paths'),
        ])
        ->alias('lag_admin.grid.collection.metadata_factory', GridCollectionMetadataFactoryInterface::class)
    ;
    $services->set(GridCollectionAttributeMetadataFactory::class)
        ->decorate('lag_admin.grid.collection_metadata_factory', priority: 255)
        ->args([
            '$metadataFactory' => service('.inner'),
            '$paths' => service('lag_admin.mapping_paths'),
        ])
    ;
    $services->set(GridMetadataFactoryInterface::class, GridMetadataFactory::class)
        ->alias('lag_admin.grid.metadata_factory', GridMetadataFactoryInterface::class)
    ;
    $services->set(GridProviderMetadataFactory::class)
        ->decorate(GridMetadataFactoryInterface::class, priority: 255)
    ;
};
