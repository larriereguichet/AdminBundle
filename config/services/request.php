<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Request\ContextBuilder\ContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use LAG\AdminBundle\Request\ContextBuilder\JsonContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\PaginationContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\PartialContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\SortingContextBuilder;
use LAG\AdminBundle\Request\Extractor\ParametersExtractor;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Request\Uri\UrlVariablesExtractor;
use LAG\AdminBundle\Request\Uri\UrlVariablesExtractorInterface;
use LAG\AdminBundle\Request\ValueResolver\GridValueResolver;
use LAG\AdminBundle\Request\ValueResolver\OperationValueResolver;
use LAG\AdminBundle\Request\ValueResolver\ResourceValueResolver;
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
    $services->set(GridValueResolver::class)
        ->args([
            '$resourceContext' => service('lag_admin.resource.context'),
            '$gridFactory' => service('lag_admin.grid.factory'),
        ])
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
    $services->set(ContextBuilderInterface::class, ContextBuilder::class);
    $services->set(SortingContextBuilder::class)
        ->decorate(ContextBuilderInterface::class)
    ;
    $services->set(JsonContextBuilder::class)
        ->decorate(ContextBuilderInterface::class)
    ;
    $services->set(PartialContextBuilder::class)
        ->decorate(ContextBuilderInterface::class)
    ;
    $services->set(PaginationContextBuilder::class)
        ->decorate(ContextBuilderInterface::class)
    ;
};
