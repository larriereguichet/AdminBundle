<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Request\ContextBuilder\AjaxContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\CompositeContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\OperationContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use LAG\AdminBundle\Request\ContextBuilder\FilterContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\PaginationContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\PartialContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\SortingContextBuilder;
use LAG\AdminBundle\Request\Extractor\ResourceParametersExtractor;
use LAG\AdminBundle\Request\Extractor\ResourceParametersExtractorInterface;
use LAG\AdminBundle\Request\Resolver\OperationValueResolver;
use LAG\AdminBundle\Request\Resolver\ResourceValueResolver;
use LAG\AdminBundle\Request\Uri\UrlVariablesExtractor;
use LAG\AdminBundle\Request\Uri\UrlVariablesExtractorInterface;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Response\Handler\CompositeResponseHandler;
use LAG\AdminBundle\Response\Handler\ContextResponseHandler;
use LAG\AdminBundle\Response\Handler\JsonResponseHandler;
use LAG\AdminBundle\Response\Handler\RedirectRespondHandler;
use LAG\AdminBundle\Response\Handler\ResponseHandler;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use LAG\AdminBundle\Response\Handler\TemplateResponseHandler;
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
    $services->set(UrlVariablesExtractorInterface::class, UrlVariablesExtractor::class);

    // Request context builders
    $services->set(ContextBuilderInterface::class, CompositeContextBuilder::class)
        ->arg('$contextBuilders', tagged_iterator(ContextBuilderInterface::SERVICE_TAG))
        ->alias('lag_admin.request.context_builder', ContextBuilderInterface::class)
    ;
    $services->set(SortingContextBuilder::class)
        ->tag(ContextBuilderInterface::SERVICE_TAG, ['priority' => 200])
    ;
    $services->set(AjaxContextBuilder::class)
        ->tag(ContextBuilderInterface::SERVICE_TAG, ['priority' => 200])
    ;
    $services->set(FilterContextBuilder::class)
        ->tag(ContextBuilderInterface::SERVICE_TAG, ['priority' => 200])
        ->arg('$formFactory', service('form.factory'))
    ;
    $services->set(PartialContextBuilder::class)
        ->tag(ContextBuilderInterface::SERVICE_TAG, ['priority' => 200])
    ;
    $services->set(PaginationContextBuilder::class)
        ->tag(ContextBuilderInterface::SERVICE_TAG, ['priority' => 200])
    ;

    // Response handlers
    $services->set(ResponseHandlerInterface::class, CompositeResponseHandler::class)
        ->arg('$responseHandlers', tagged_iterator('lag_admin.response_handler', exclude: [
            CompositeResponseHandler::class,
            ContextResponseHandler::class,
        ]))
        ->tag('lag_admin.response_handler')
        ->alias('lag_admin.response_handler', ResponseHandlerInterface::class)
    ;
    $services->set(ContextResponseHandler::class)
        ->decorate(id: ResponseHandlerInterface::class, priority: -250)
        ->arg('$responseHandler', service('.inner'))
        ->arg('$contextBuilder', service('lag_admin.request.context_builder'))
    ;
    $services->set(JsonResponseHandler::class)
        ->arg('$serializer', service('serializer'))
        ->tag('lag_admin.response_handler')
    ;
    $services->set(RedirectRespondHandler::class)
        ->arg('$urlGenerator', service(UrlGeneratorInterface::class))
        ->tag('lag_admin.response_handler')
    ;
    $services->set(TemplateResponseHandler::class)
        ->arg('$environment', service('twig'))
        ->tag('lag_admin.response_handler')
    ;
};
