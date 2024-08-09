<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Request\Context\AjaxContextProvider;
use LAG\AdminBundle\Request\Context\CompositeContextProvider;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Context\FilterContextProvider;
use LAG\AdminBundle\Request\Context\SortingContextProvider;
use LAG\AdminBundle\Request\Extractor\ResourceParametersExtractor;
use LAG\AdminBundle\Request\Extractor\ResourceParametersExtractorInterface;
use LAG\AdminBundle\Request\Resolver\OperationValueResolver;
use LAG\AdminBundle\Request\Resolver\ResourceValueResolver;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractor;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Response\Handler\RedirectHandler;
use LAG\AdminBundle\Response\Handler\RedirectHandlerInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Request parameters value resolvers
    $services->set(ResourceValueResolver::class)
        ->arg('$resourceContext', service(ResourceContextInterface::class))
        ->tag('controller.argument_value_resolver')
    ;

    $services->set(OperationValueResolver::class)
        ->arg('$resourceContext', service(ResourceContextInterface::class))
        ->tag('controller.argument_value_resolver')
    ;

    // Resource request parameters extractors
    $services->set(ResourceParametersExtractorInterface::class, ResourceParametersExtractor::class)
        ->arg('$applicationParameter', param('lag_admin.application_parameter'))
        ->arg('$resourceParameter', param('lag_admin.resource_parameter'))
        ->arg('$operationParameter', param('lag_admin.operation_parameter'))
    ;
    $services->set(UriVariablesExtractorInterface::class, UriVariablesExtractor::class);

    // Request context providers
    $services->set(ContextProviderInterface::class, CompositeContextProvider::class)
        ->arg('$contextProviders', tagged_iterator('lag_admin.request_context_provider'))
    ;
    $services->set(SortingContextProvider::class)
        ->tag('lag_admin.request_context_provider', ['priority' => 255])
    ;
    $services->set(AjaxContextProvider::class)
        ->tag('lag_admin.request_context_provider', ['priority' => 255])
    ;
    $services->set(FilterContextProvider::class)
        ->arg('$formFactory', service('form.factory'))
        ->tag('lag_admin.request_context_provider', ['priority' => 255])
    ;

    // Redirection handlers
    $services->set(RedirectHandlerInterface::class, RedirectHandler::class)
        ->arg('$urlGenerator', service(UrlGeneratorInterface::class))
    ;
};
