<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Metadata\Context\ResourceContext;
use LAG\AdminBundle\Metadata\Context\ResourceContextInterface;
use LAG\AdminBundle\Metadata\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Metadata\Locator\AttributeLocator;
use LAG\AdminBundle\Metadata\Locator\CompositeLocator;
use LAG\AdminBundle\Metadata\Locator\MetadataLocatorInterface;
use LAG\AdminBundle\Metadata\Registry\CacheRegistry;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistry;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Resource registries
    $services->set(ResourceRegistryInterface::class, ResourceRegistry::class)
        ->arg('$resources', expr('service("lag_admin.resource.resolver").resolveResourceCollectionFromLocators()'))
        ->arg('$defaultApplicationName', param('lag_admin.application_name'))
    ;
    $services->set(CacheRegistry::class)
        ->decorate(ResourceRegistryInterface::class)
        ->arg('$registry', service('.inner'))
        ->arg('$defaultApplicationName', param('lag_admin.application_name'))
    ;

    // Metadata locators
    $services->set(MetadataLocatorInterface::class, CompositeLocator::class)
        ->arg('$locators', tagged_iterator('lag_admin.resource.locator'))
        ->arg('$kernel', service('kernel'))
    ;
    $services->set(AttributeLocator::class)
        ->tag('lag_admin.resource.locator')
    ;

    // Request context
    $services->set(ResourceContextInterface::class, ResourceContext::class)
        ->arg('$parametersExtractor', service(ParametersExtractorInterface::class))
        ->arg('$resourceRegistry', service(ResourceRegistryInterface::class))
    ;
};
