<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Context\ApplicationContext;
use LAG\AdminBundle\Resource\Context\ApplicationContextInterface;
use LAG\AdminBundle\Resource\Context\OperationContext;
use LAG\AdminBundle\Resource\Context\OperationContextInterface;
use LAG\AdminBundle\Resource\Context\ResourceContext;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\DataMapper\DataMapper;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;
use LAG\AdminBundle\Resource\Factory\ApplicationFactory;
use LAG\AdminBundle\Resource\Factory\ApplicationFactoryInterface;
use LAG\AdminBundle\Resource\Factory\CacheApplicationFactory;
use LAG\AdminBundle\Resource\Factory\CacheResourceFactory;
use LAG\AdminBundle\Resource\Factory\DefinitionFactory;
use LAG\AdminBundle\Resource\Factory\DefinitionFactoryInterface;
use LAG\AdminBundle\Resource\Factory\OperationFactory;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Resource\Factory\ResourceFactory;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Resource\Initializer\ActionInitializer;
use LAG\AdminBundle\Resource\Initializer\ActionInitializerInterface;
use LAG\AdminBundle\Resource\Initializer\CollectionOperationInitializer;
use LAG\AdminBundle\Resource\Initializer\CollectionOperationInitializerInterface;
use LAG\AdminBundle\Resource\Initializer\OperationDefaultsInitializer;
use LAG\AdminBundle\Resource\Initializer\OperationDefaultsInitializerInterface;
use LAG\AdminBundle\Resource\Initializer\OperationFormInitializeInterface;
use LAG\AdminBundle\Resource\Initializer\OperationFormInitializer;
use LAG\AdminBundle\Resource\Initializer\OperationInitializer;
use LAG\AdminBundle\Resource\Initializer\OperationInitializerInterface;
use LAG\AdminBundle\Resource\Initializer\OperationRoutingInitializer;
use LAG\AdminBundle\Resource\Initializer\OperationRoutingInitializerInterface;
use LAG\AdminBundle\Resource\Initializer\PropertyInitializer;
use LAG\AdminBundle\Resource\Initializer\PropertyInitializerInterface;
use LAG\AdminBundle\Resource\Initializer\ResourceInitializer;
use LAG\AdminBundle\Resource\Initializer\ResourceInitializerInterface;
use LAG\AdminBundle\Resource\Locator\AttributePropertyLocator;
use LAG\AdminBundle\Resource\Locator\CompositePropertyLocator;
use LAG\AdminBundle\Resource\Locator\PropertyLocatorInterface;
use LAG\AdminBundle\Resource\PropertyGuesser\PropertyGuesser;
use LAG\AdminBundle\Resource\PropertyGuesser\PropertyGuesserInterface;
use LAG\AdminBundle\Resource\PropertyGuesser\ResourcePropertyGuesser;
use LAG\AdminBundle\Resource\PropertyGuesser\ResourcePropertyGuesserInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Twig\Globals\LAGAdminGlobal;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Application factories
    $services->set(ApplicationFactoryInterface::class, ApplicationFactory::class)
        ->args([
            '$definitionFactory' => service(DefinitionFactoryInterface::class),
        ])
        ->alias('lag_admin.application.factory', ApplicationFactoryInterface::class)
    ;
    $services->set(CacheApplicationFactory::class)
        ->args([
            '$applicationFactory' => service('.inner'),
        ])
        ->decorate('lag_admin.application.factory')
    ;

    // Resources factories
    $services->set(ResourceFactoryInterface::class, ResourceFactory::class)
        ->args([
            '$definitionFactory' => service(DefinitionFactoryInterface::class),
            '$resourceInitializer' => service(ResourceInitializerInterface::class),
            '$validator' => service('validator'),
        ])
        ->alias('lag_admin.resource.factory', ResourceFactoryInterface::class)
    ;
    $services->set(CacheResourceFactory::class)
        ->decorate(ResourceFactoryInterface::class)
        ->args([
            '$resourceFactory' => service('.inner'),
        ])
    ;

    // Operation factories
    $services->set(OperationFactoryInterface::class, OperationFactory::class)
        ->args([
            '$resourceFactory' => service('lag_admin.resource.factory'),
        ])
        ->alias('lag_admin.operation.factory', OperationFactoryInterface::class)
    ;

    // Definition factories
    $services->set(DefinitionFactoryInterface::class, DefinitionFactory::class)
        ->alias('lag_admin.definition.factory', DefinitionFactoryInterface::class)
    ;

    // Properties locators
    $services->set(PropertyLocatorInterface::class, CompositePropertyLocator::class)
        ->args(['$locators' => tagged_iterator('lag_admin.property_locator')])
    ;
    $services->set(AttributePropertyLocator::class)
        ->tag('lag_admin.property_locator')
    ;

    // Contexts
    $services->set(LAGAdminGlobal::class)
        ->args([
            '$applicationContext' => service('lag_admin.application.context'),
            '$resourceContext' => service('lag_admin.resource.context'),
            '$operationContext' => service('lag_admin.operation.context'),
        ])
        ->alias('lag_admin.twig.global', LAGAdminGlobal::class)
    ;
    $services->set(ApplicationContextInterface::class, ApplicationContext::class)
        ->args([
            '$requestStack' => service('request_stack'),
            '$parametersExtractor' => service(ParametersExtractorInterface::class),
            '$applicationFactory' => service('lag_admin.application.factory'),
        ])
        ->alias('lag_admin.application.context', ApplicationContextInterface::class)
    ;
    $services->set(ResourceContextInterface::class, ResourceContext::class)
        ->args([
            '$requestStack' => service('request_stack'),
            '$parametersExtractor' => service(ParametersExtractorInterface::class),
            '$resourceFactory' => service('lag_admin.resource.factory'),
        ])
        ->alias('lag_admin.resource.context', ResourceContextInterface::class)
    ;
    $services->set(OperationContextInterface::class, OperationContext::class)
        ->args([
            '$requestStack' => service('request_stack'),
            '$parametersExtractor' => service(ParametersExtractorInterface::class),
            '$operationFactory' => service('lag_admin.operation.factory'),
        ])
        ->alias('lag_admin.operation.context', OperationContextInterface::class)
    ;

    // Mappers
    $services->set(DataMapperInterface::class, DataMapper::class);

    // Property guessers
    $services->set(ResourcePropertyGuesserInterface::class, ResourcePropertyGuesser::class)
        ->arg('$propertyGuesser', service(PropertyGuesserInterface::class))
    ;
    $services->set(PropertyGuesserInterface::class, PropertyGuesser::class);

    // Initializers
    $services->set(ResourceInitializerInterface::class, ResourceInitializer::class)
        ->args([
            '$applicationFactory' => service('lag_admin.application.factory'),
            '$operationInitializer' => service(OperationInitializerInterface::class),
            '$propertyInitializer' => service(PropertyInitializerInterface::class),
            '$propertyGuesser' => service(ResourcePropertyGuesserInterface::class),
        ])
    ;
    $services->set(OperationInitializerInterface::class, OperationInitializer::class)
        ->args([
            '$defaultsOperationInitializer' => service(OperationDefaultsInitializerInterface::class),
            '$collectionOperationInitializer' => service(CollectionOperationInitializerInterface::class),
            '$operationFormInitializer' => service(OperationFormInitializeInterface::class),
            '$operationRoutingInitializer' => service(OperationRoutingInitializerInterface::class),
        ])
    ;
    $services->set(CollectionOperationInitializerInterface::class, CollectionOperationInitializer::class)
        ->args([
            '$actionInitializer' => service(ActionInitializerInterface::class),
        ])
    ;
    $services->set(OperationDefaultsInitializerInterface::class, OperationDefaultsInitializer::class);
    $services->set(OperationFormInitializeInterface::class, OperationFormInitializer::class);
    $services->set(OperationRoutingInitializerInterface::class, OperationRoutingInitializer::class)
        ->args([
            '$routeNameGenerator' => service(RouteNameGeneratorInterface::class),
        ]);
    $services->set(PropertyInitializerInterface::class, PropertyInitializer::class);
    $services->set(ActionInitializerInterface::class, ActionInitializer::class);
};
