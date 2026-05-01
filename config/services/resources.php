<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Metadata\Factory\ResourceMetadataFactoryInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Context\ResourceContext;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\DataMapper\DataMapper;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;
use LAG\AdminBundle\Resource\Factory\OperationFactory;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Resource\Factory\ResourceCollectionFactory;
use LAG\AdminBundle\Resource\Factory\ResourceCollectionFactoryInterface;
use LAG\AdminBundle\Resource\Factory\ResourceFactory;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Resources factories
    $services->set(ResourceCollectionFactoryInterface::class, ResourceCollectionFactory::class)
        ->args([
            '$metadataCollectionFactory' => service('lag_admin.resource.collection_metadata_factory'),
            '$resourceFactory' => service('lag_admin.resource.factory'),
        ])
        ->alias('lag_admin.resource.collection_factory', ResourceCollectionFactoryInterface::class)
    ;
    $services->set(ResourceFactoryInterface::class, ResourceFactory::class)
        ->args([
            '$metadataFactory' => service('lag_admin.resource.metadata_factory'),
            '$validator' => service('validator'),
        ])
        ->alias('lag_admin.resource.factory', ResourceFactoryInterface::class)
    ;
    $services->set(OperationFactoryInterface::class, OperationFactory::class)
        ->args([
            '$resourceFactory' => service('lag_admin.resource.factory'),
        ])
        ->alias('lag_admin.operation.factory', OperationFactoryInterface::class)
    ;

    // Contexts
    $services->set(ResourceContextInterface::class, ResourceContext::class)
        ->alias('lag_admin.resource.context', ResourceContextInterface::class)
    ;

    // Mappers
    $services->set(DataMapperInterface::class, DataMapper::class)
        ->args([
            '$propertyAccessor' => service('property_accessor'),
        ])
    ;
};
