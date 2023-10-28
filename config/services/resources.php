<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Metadata\Context\ResourceContext;
use LAG\AdminBundle\Metadata\Context\ResourceContextInterface;
use LAG\AdminBundle\Metadata\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Metadata\Locator\AttributeLocator;
use LAG\AdminBundle\Metadata\Locator\CompositeLocator;
use LAG\AdminBundle\Metadata\Locator\MetadataLocatorInterface;
use LAG\AdminBundle\Metadata\Registry\CacheRegistryDecorator;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistry;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Resource registries
    $services->set(ResourceRegistryInterface::class, ResourceRegistry::class)
        ->arg('$resourcePaths', param('lag_admin.resource_paths'))
        ->arg('$locator', service(MetadataLocatorInterface::class))
        ->arg('$resourceFactory', service(ResourceFactoryInterface::class))
    ;
    $services->set(CacheRegistryDecorator::class)
        ->decorate(ResourceRegistryInterface::class)
        ->arg('$decorated', service('.inner'))
    ;

    // Metadata attributes locators
    $services->set(MetadataLocatorInterface::class, CompositeLocator::class)
        ->arg('$locators', tagged_iterator('lag_admin.resource.locator'))
        ->arg('$kernel', service('kernel'))
    ;
    $services->set(AttributeLocator::class)
        ->tag('lag_admin.resource.locator')
    ;

    // Resource request context
    $services->set(ResourceContextInterface::class, ResourceContext::class)
        ->arg('$parametersExtractor', service(ParametersExtractorInterface::class))
        ->arg('$resourceRegistry', service(ResourceRegistryInterface::class))
    ;
};
