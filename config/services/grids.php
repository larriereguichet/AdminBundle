<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Grid\Builder\CellBuilder;
use LAG\AdminBundle\Grid\Builder\CellBuilderInterface;
use LAG\AdminBundle\Grid\Builder\GridViewBuilder;
use LAG\AdminBundle\Grid\Builder\GridViewBuilderInterface;
use LAG\AdminBundle\Grid\DataTransformer\CompositeDataTransformer;
use LAG\AdminBundle\Grid\DataTransformer\CountableDataTransformer;
use LAG\AdminBundle\Grid\DataTransformer\FormDataTransformer;
use LAG\AdminBundle\Grid\DataTransformer\MapDataTransformer;
use LAG\AdminBundle\Grid\DataTransformer\PropertyDataTransformerInterface;
use LAG\AdminBundle\Grid\Registry\GridRegistry;
use LAG\AdminBundle\Grid\Registry\GridRegistryInterface;
use LAG\AdminBundle\Grid\Render\CellRenderer;
use LAG\AdminBundle\Grid\Render\CellRendererInterface;
use LAG\AdminBundle\Grid\Render\GridRenderer;
use LAG\AdminBundle\Grid\Render\GridRendererInterface;
use LAG\AdminBundle\Resource\Locator\GridLocator;
use LAG\AdminBundle\Resource\Resolver\ClassResolverInterface;
use LAG\AdminBundle\Resource\Resolver\GridResolver;
use LAG\AdminBundle\Resource\Resolver\GridResolverInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Builders
    $services->set(GridViewBuilderInterface::class, GridViewBuilder::class)
        ->arg('$registry', service(GridRegistryInterface::class))
        ->arg('$cellFactory', service(CellBuilderInterface::class))
        ->arg('$eventDispatcher', service('lag_admin.event_dispatcher'))
        ->arg('$validator', service('validator'))
    ;
    $services->set(CellBuilderInterface::class, CellBuilder::class)
        ->arg('$dataTransformer', service(PropertyDataTransformerInterface::class))
        ->arg('$validator', service('validator'))
    ;

    // Renderers
    $services->set(GridRendererInterface::class, GridRenderer::class)
        ->arg('$environment', service('twig'))
    ;
    $services->set(CellRendererInterface::class, CellRenderer::class)
        ->arg('$environment', service('twig'))
    ;

    // Data transformers
    $services->set(PropertyDataTransformerInterface::class, CompositeDataTransformer::class)
        ->arg('$dataTransformers', tagged_iterator('lag_admin.grid.data_transformers'))
    ;
    $services->set(CountableDataTransformer::class)
        ->tag('lag_admin.grid.data_transformers')
    ;
    $services->set(MapDataTransformer::class)
        ->tag('lag_admin.grid.data_transformers')
    ;
    $services->set(FormDataTransformer::class)
        ->arg('$formFactory', service('form.factory'))
        ->tag('lag_admin.grid.data_transformers')
    ;

    // Resolvers
    $services->set(GridResolverInterface::class, GridResolver::class)
        ->arg('$classResolver', service(ClassResolverInterface::class))
        ->public()
    ;
    $services->alias('lag_admin.grid_resolver', GridResolverInterface::class)
        ->public()
    ;

    // Locators
    $services->set(GridLocator::class)
        ->arg('$registry', service(GridRegistryInterface::class))
        ->tag('lag_admin.metadata_locator')
    ;

    // Registry
    $services->set(GridRegistryInterface::class, GridRegistry::class)
        ->arg('$grids', expr('service("lag_admin.grid_resolver").resolveGrids(parameter("lag_admin.resource_paths"))'))
    ;
};
