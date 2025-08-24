<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Request\ContextBuilder\AjaxContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\CompositeContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use LAG\AdminBundle\Request\ContextBuilder\OperationContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\PaginationContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\PartialContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\SortingContextBuilder;
use LAG\AdminBundle\Request\Extractor\ParametersExtractor;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Request\Resolver\OperationValueResolver;
use LAG\AdminBundle\Request\Resolver\ResourceValueResolver;
use LAG\AdminBundle\Request\Uri\UrlVariablesExtractor;
use LAG\AdminBundle\Request\Uri\UrlVariablesExtractorInterface;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Request parameters value resolvers
    $services->set(ResourceValueResolver::class)
        ->arg('$resourceContext', service(ResourceContextInterface::class))
        ->tag('controller.argument_value_resolver')
    ;

    $services->set(OperationValueResolver::class)
        ->arg('$parametersExtractor', service('lag_admin.request.parameters_extractor'))
        ->arg('$operationContext', service('lag_admin.operation.context'))
        ->tag('controller.argument_value_resolver')
    ;

    // Resource request parameters extractors
    $services->set(ParametersExtractorInterface::class, ParametersExtractor::class)
        ->args([
            '$applicationParameter' => param('lag_admin.application_parameter'),
            '$resourceParameter' => param('lag_admin.resource_parameter'),
            '$operationParameter' => param('lag_admin.operation_parameter'),
        ])
        ->alias('lag_admin.request.parameters_extractor', ParametersExtractorInterface::class)
    ;
    $services->set(UrlVariablesExtractorInterface::class, UrlVariablesExtractor::class);

    // Request context builders
    $services->set(ContextBuilderInterface::class, CompositeContextBuilder::class)
        ->arg('$contextBuilders', tagged_iterator(ContextBuilderInterface::SERVICE_TAG))
        ->alias('lag_admin.request.context_builder', ContextBuilderInterface::class)
    ;
    $services->set(OperationContextBuilder::class)
        ->tag(ContextBuilderInterface::SERVICE_TAG, ['priority' => 200])
    ;
    $services->set(SortingContextBuilder::class)
        ->tag(ContextBuilderInterface::SERVICE_TAG, ['priority' => 200])
    ;
    $services->set(AjaxContextBuilder::class)
        ->tag(ContextBuilderInterface::SERVICE_TAG, ['priority' => 200])
    ;
    $services->set(PartialContextBuilder::class)
        ->tag(ContextBuilderInterface::SERVICE_TAG, ['priority' => 200])
    ;
    $services->set(PaginationContextBuilder::class)
        ->tag(ContextBuilderInterface::SERVICE_TAG, ['priority' => 200])
    ;
};
