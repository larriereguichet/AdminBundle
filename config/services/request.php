<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Metadata\Context\ResourceContextInterface;
use LAG\AdminBundle\Request\Context\AjaxContextProvider;
use LAG\AdminBundle\Request\Context\CompositeContextProvider;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Context\FilterContextProvider;
use LAG\AdminBundle\Request\Context\SortingContextProvider;
use LAG\AdminBundle\Request\Extractor\ParametersExtractor;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Request\Resolver\OperationValueResolver;
use LAG\AdminBundle\Request\Resolver\ResourceValueResolver;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractor;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
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
    $services->set(ParametersExtractorInterface::class, ParametersExtractor::class);
    $services->set(UriVariablesExtractorInterface::class, UriVariablesExtractor::class);

    $services->set(ContextProviderInterface::class, CompositeContextProvider::class)
        ->arg('$contextProviders', tagged_iterator(ContextProviderInterface::class))
    ;

    // Request context providers
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
