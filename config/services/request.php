<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Request\ContextBuilder\AjaxContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use LAG\AdminBundle\Request\ContextBuilder\FilterContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\PartialContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\SortingContextBuilder;
use LAG\AdminBundle\Request\Extractor\ResourceParametersExtractor;
use LAG\AdminBundle\Request\Extractor\ResourceParametersExtractorInterface;
use LAG\AdminBundle\Request\Resolver\OperationValueResolver;
use LAG\AdminBundle\Request\Resolver\ResourceValueResolver;
use LAG\AdminBundle\Request\Uri\UrlVariablesExtractor;
use LAG\AdminBundle\Request\Uri\UrlVariablesExtractorInterface;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Response\Handler\JsonResponseHandler;
use LAG\AdminBundle\Response\Handler\ResponseHandler;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
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
    $services->set(ContextBuilderInterface::class, ContextBuilder::class);
    $services->set(SortingContextBuilder::class)
        ->decorate(id: ContextBuilderInterface::class, priority: 200)
        ->arg('$contextBuilder', service('.inner'))
    ;
    $services->set(AjaxContextBuilder::class)
        ->decorate(id: ContextBuilderInterface::class, priority: 200)
        ->arg('$contextBuilder', service('.inner'))
    ;
    $services->set(FilterContextBuilder::class)
        ->decorate(id: ContextBuilderInterface::class, priority: 200)
        ->arg('$contextBuilder', service('.inner'))
        ->arg('$formFactory', service('form.factory'))
    ;
    $services->set(PartialContextBuilder::class)
        ->decorate(id: ContextBuilderInterface::class, priority: 200)
        ->arg('$contextBuilder', service('.inner'))
    ;

    // Response handlers
    $services->set(ResponseHandlerInterface::class, ResponseHandler::class)
        ->arg('$environment', service('twig'))
        ->arg('$urlGenerator', service(UrlGeneratorInterface::class))
        ->alias('lag_admin.response_handler', ResponseHandlerInterface::class)
    ;
    $services->set(JsonResponseHandler::class)
        ->arg('$responseHandler', service('.inner'))
        ->arg('$serializer', service('serializer'))
        ->decorate(ResponseHandlerInterface::class)
    ;
};
