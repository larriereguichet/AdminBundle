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
use LAG\AdminBundle\Metadata\Factory\OperationsFormMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\PropertyCollectionClassMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\PropertyCollectionMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\PropertyCollectionMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\PropertyMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\PropertyMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\ResourceCollectionAttributeMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\ResourceCollectionMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\ResourceCollectionMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\ResourceMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\ResourceMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\OperationsLinkMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\OperationsMetadataFactory;
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
        ->args([
            '$paths' => param('lag_admin.mapping_paths'),
            '$kernelEnvironment' => param('kernel.environment'),
        ])
        ->alias('lag_admin.resource.collection_metadata_factory', ResourceCollectionMetadataFactoryInterface::class)
    ;
    $services->set(ResourceCollectionAttributeMetadataFactory::class)
        ->decorate('lag_admin.resource.collection_metadata_factory')
        ->args([
            '$metadataFactory' => service('.inner'),
            '$paths' => param('lag_admin.mapping_paths'),
        ])
    ;

    // Resource factories
    $services->set(ResourceMetadataFactoryInterface::class, ResourceMetadataFactory::class)
        ->args([
            '$collectionMetadataFactory' => service('lag_admin.resource.collection_metadata_factory'),
            '$applicationFactory' => service('lag_admin.application.metadata_factory'),
        ])
        ->alias('lag_admin.resource.metadata_factory', ResourceMetadataFactoryInterface::class)
    ;
    $services->set(OperationsMetadataFactory::class)
        ->decorate('lag_admin.resource.metadata_factory', priority: 255)
        ->args([
            '$metadataFactory' => service('.inner'),
            '$applicationMetadataFactory' => service('lag_admin.application.metadata_factory'),
            '$routeNameGenerator' => service(RouteNameGeneratorInterface::class),
        ])
    ;
    $services->set(CollectionOperationMetadataFactory::class)
        ->decorate('lag_admin.resource.metadata_factory', priority: 250)
        ->args([
            '$metadataFactory' => service('.inner'),
        ])
    ;
    $services->set(ResourcePropertiesMetadataFactory::class)
        ->decorate('lag_admin.resource.metadata_factory', priority: 245)
        ->args([
            '$metadataFactory' => service('.inner'),
            '$propertyCollectionMetadataFactory' => service('lag_admin.property.collection_metadata_factory'),
        ])
    ;
    $services->set(OperationsLinkMetadataFactory::class)
        ->decorate('lag_admin.resource.metadata_factory', priority: -255)
        ->args([
            '$metadataFactory' => service('.inner'),
        ])
    ;
    $services->set(OperationsFormMetadataFactory::class)
        ->decorate('lag_admin.resource.metadata_factory', priority: -255)
        ->args([
            '$metadataFactory' => service('.inner'),
        ])
    ;

    // Properties factories
    $services->set(PropertyCollectionMetadataFactoryInterface::class, PropertyCollectionMetadataFactory::class)
        ->alias('lag_admin.property.collection_metadata_factory', PropertyCollectionMetadataFactoryInterface::class)
    ;
    $services->set(PropertyCollectionClassMetadataFactory::class)
        ->args([
            '$metadataFactory' => service('.inner'),
        ])
        ->decorate('lag_admin.property.collection_metadata_factory', priority: -255)
    ;

    // Grid factories
    $services->set(GridCollectionMetadataFactoryInterface::class, GridCollectionMetadataFactory::class)
        ->args([
            '$paths' => param('lag_admin.mapping_paths'),
            '$kernelEnvironment' => param('kernel.environment'),
        ])
        ->alias('lag_admin.grid.collection_metadata_factory', GridCollectionMetadataFactoryInterface::class)
    ;
    $services->set(GridCollectionAttributeMetadataFactory::class)
        ->decorate('lag_admin.grid.collection_metadata_factory', priority: 255)
        ->args([
            '$metadataFactory' => service('.inner'),
            '$paths' => param('lag_admin.mapping_paths'),
        ])
    ;
    $services->set(GridMetadataFactoryInterface::class, GridMetadataFactory::class)
        ->args([
            '$metadataFactory' => service('lag_admin.grid.collection_metadata_factory'),
            '$gridTemplates' => param('lag_admin.grid_templates'),
        ])
        ->alias('lag_admin.grid.metadata_factory', GridMetadataFactoryInterface::class)
    ;
    $services->set(GridProviderMetadataFactory::class)
        ->decorate(GridMetadataFactoryInterface::class, priority: 255)
        ->args([
            '$builders' => tagged_iterator('lag_admin.grid_provider'),
            '$metadataFactory' => service('.inner'),
        ])
    ;
};
