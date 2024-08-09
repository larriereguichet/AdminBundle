<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Request\Extractor\ResourceParametersExtractorInterface;
use LAG\AdminBundle\Resource\Context\ResourceContext;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\DataMapper\DataMapper;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Resource\Locator\PropertyLocator;
use LAG\AdminBundle\Resource\Locator\PropertyLocatorInterface;
use LAG\AdminBundle\Resource\Locator\ResourceLocator;
use LAG\AdminBundle\Resource\Locator\ResourceLocatorInterface;
use LAG\AdminBundle\Resource\Registry\ApplicationRegistry;
use LAG\AdminBundle\Resource\Registry\ApplicationRegistryInterface;
use LAG\AdminBundle\Resource\Registry\CacheRegistry;
use LAG\AdminBundle\Resource\Registry\ResourceRegistry;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Resource\Resolver\ClassResolver;
use LAG\AdminBundle\Resource\Resolver\ClassResolverInterface;
use LAG\AdminBundle\Resource\Resolver\PhpFileResolver;
use LAG\AdminBundle\Resource\Resolver\PhpFileResolverInterface;
use LAG\AdminBundle\Resource\Resolver\ResourceResolver;
use LAG\AdminBundle\Resource\Resolver\ResourceResolverInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Registries
    $services->set(ResourceRegistryInterface::class, ResourceRegistry::class)
        ->arg('$resources', expr('service("lag_admin.resource_resolver").resolveResources(parameter("lag_admin.resource_paths"))'))
        ->arg('$defaultApplication', param('lag_admin.application_name'))
        ->arg('$factory', service(ResourceFactoryInterface::class))
    ;
    $services->set(ApplicationRegistryInterface::class, ApplicationRegistry::class);

    if ($container->env() === 'prod') {
        $services->set(CacheRegistry::class)
            ->decorate(ResourceRegistryInterface::class)
            ->arg('$registry', service('.inner'))
            ->arg('$defaultApplication', param('lag_admin.application_name'))
        ;
    }

    // Metadata locators
    $services->set(ResourceLocatorInterface::class, ResourceLocator::class)
        ->arg('$defaultApplication', param('lag_admin.application_name'))
        ->tag('lag_admin.resource_locator')
    ;
    $services->set(PropertyLocatorInterface::class, PropertyLocator::class)
        ->tag('lag_admin.metadata_locator')
    ;

    // Request context
    $services->set(ResourceContextInterface::class, ResourceContext::class)
        ->arg('$parametersExtractor', service(ResourceParametersExtractorInterface::class))
        ->arg('$resourceRegistry', service(ResourceRegistryInterface::class))
    ;

    // Resolvers
    $services->set(ResourceResolverInterface::class, ResourceResolver::class)
        ->arg('$classResolver', service(ClassResolverInterface::class))
        ->arg('$resourceLocator', service(ResourceLocatorInterface::class))
        ->arg('$propertyLocator', service(PropertyLocatorInterface::class))
        ->public()
    ;
    $services->alias('lag_admin.resource_resolver', ResourceResolverInterface::class)
        ->public()
    ;
    $services->set(ClassResolverInterface::class, ClassResolver::class);
    $services->set(PhpFileResolverInterface::class, PhpFileResolver::class);

    // Mappers
    $services->set(DataMapperInterface::class, DataMapper::class);
};
