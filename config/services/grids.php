<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Bridge\LiipImagine\DataTransformer\ImageDataTransformer;
use LAG\AdminBundle\Condition\ConditionMatcherInterface;
use LAG\AdminBundle\Grid\DataTransformer\CountDataTransformer;
use LAG\AdminBundle\Grid\DataTransformer\FormDataTransformer;
use LAG\AdminBundle\Grid\DataTransformer\MapDataTransformer;
use LAG\AdminBundle\Grid\Registry\DataTransformerRegistry;
use LAG\AdminBundle\Grid\Registry\DataTransformerRegistryInterface;
use LAG\AdminBundle\Grid\Registry\GridRegistry;
use LAG\AdminBundle\Grid\Registry\GridRegistryInterface;
use LAG\AdminBundle\Grid\Resolver\GridResolver;
use LAG\AdminBundle\Grid\Resolver\GridResolverInterface;
use LAG\AdminBundle\Grid\ViewBuilder\ActionViewBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\ActionViewBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\CellViewBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\CellViewBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilderInterface;
use LAG\AdminBundle\Resource\Resolver\ClassResolverInterface;
use LAG\AdminBundle\Resource\Resolver\PhpFileResolverInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\View\Render\CellRenderer;
use LAG\AdminBundle\View\Render\CellRendererInterface;
use LAG\AdminBundle\View\Render\GridRenderer;
use LAG\AdminBundle\View\Render\GridRendererInterface;
use Liip\ImagineBundle\LiipImagineBundle;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // View builders
    $services->set(GridViewBuilderInterface::class, GridViewBuilder::class)
        ->arg('$cellBuilder', service(CellViewBuilderInterface::class))
        ->arg('$actionBuilder', service(ActionViewBuilderInterface::class))
        ->arg('$eventDispatcher', service('lag_admin.event_dispatcher'))
        ->arg('$validator', service('validator'))
    ;
    $services->set(CellViewBuilderInterface::class, CellViewBuilder::class)
        ->arg('$dataTransformerRegistry', service(DataTransformerRegistryInterface::class))
    ;
    $services->set(ActionViewBuilderInterface::class, ActionViewBuilder::class)
        ->arg('$urlGenerator', service(UrlGeneratorInterface::class))
        ->arg('$conditionMatcher', service(ConditionMatcherInterface::class))
        ->arg('$translator', service('translator'))
    ;

    // Renderers
    $services->set(GridRendererInterface::class, GridRenderer::class)
        ->arg('$environment', service('twig'))
    ;
    $services->set(CellRendererInterface::class, CellRenderer::class)
        ->arg('$environment', service('twig'))
    ;

    // Data transformers
    $services->set(CountDataTransformer::class)
        ->tag('lag_admin.data_transformer')
    ;
    $services->set(MapDataTransformer::class)
        ->tag('lag_admin.data_transformer')
    ;
    $services->set(FormDataTransformer::class)
        ->arg('$formFactory', service('form.factory'))
        ->tag('lag_admin.data_transformer')
    ;

    if (class_exists(LiipImagineBundle::class)) {
        $container->services()
            ->set(ImageDataTransformer::class)
            ->tag('lag_admin.data_transformer')
            ->arg('$filterExtension', service('liip_imagine.templating.filter_runtime'))
        ;
    }

    // Resolvers
    $services->set(GridResolverInterface::class, GridResolver::class)
        ->arg('$classResolver', service(ClassResolverInterface::class))
        ->arg('$fileResolver', service(PhpFileResolverInterface::class))
        ->public()
    ;
    $services->alias('lag_admin.grid_resolver', GridResolverInterface::class)
        ->public()
    ;

    // Registry
    $services->set(GridRegistryInterface::class, GridRegistry::class)
        ->arg('$grids', expr('service("lag_admin.grid_resolver").resolveGrids(parameter("lag_admin.grid_paths"))'))
        ->arg('$builders', tagged_iterator('lag_admin.grid_builder'))
    ;
    $services->set(DataTransformerRegistryInterface::class, DataTransformerRegistry::class)
        ->arg('$dataTransformers', tagged_iterator('lag_admin.data_transformer'))
    ;
};
