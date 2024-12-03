<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Bridge\LiipImagine\DataTransformer\ImageDataTransformer;
use LAG\AdminBundle\Condition\Matcher\ConditionMatcherInterface;
use LAG\AdminBundle\Grid\DataTransformer\CountDataTransformer;
use LAG\AdminBundle\Grid\DataTransformer\EnumDataTransformer;
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
use LAG\AdminBundle\Grid\ViewBuilder\CollectionCellViewBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\CompoundCellViewBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\ConditionCellViewBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\DataCellViewBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\HeaderViewBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\HeaderViewBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\RowViewBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\RowViewBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\SecurityCellViewBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\SecurityHeaderViewBuilder;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;
use LAG\AdminBundle\Resource\Resolver\ClassResolverInterface;
use LAG\AdminBundle\Resource\Resolver\PhpFileResolverInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\Security\PermissionChecker\PropertyPermissionCheckerInterface;
use Liip\ImagineBundle\LiipImagineBundle;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // View builders
    $services->set(GridViewBuilderInterface::class, GridViewBuilder::class)
        ->arg('$rowBuilder', service(RowViewBuilderInterface::class))
        ->arg('$actionBuilder', service(ActionViewBuilderInterface::class))
        ->arg('$eventDispatcher', service('lag_admin.event_dispatcher'))
        ->arg('$validator', service('validator'))
    ;

    $services->set(RowViewBuilderInterface::class, RowViewBuilder::class)
        ->arg('$cellBuilder', service(CellViewBuilderInterface::class))
        ->arg('$headerBuilder', service(HeaderViewBuilderInterface::class))
        ->arg('$actionsBuilder', service(ActionViewBuilderInterface::class))
    ;

    $services->set(HeaderViewBuilderInterface::class, HeaderViewBuilder::class);
    $services->set(SecurityHeaderViewBuilder::class)
        ->decorate(id: HeaderViewBuilderInterface::class, priority: 200)
        ->arg('$headerBuilder', service('.inner'))
        ->arg('$permissionChecker', service(PropertyPermissionCheckerInterface::class))
    ;

    // Cell view builders
    $services->set(CellViewBuilderInterface::class, CellViewBuilder::class);
    $services->set(SecurityCellViewBuilder::class)
        ->decorate(id: CellViewBuilderInterface::class, priority: 200)
        ->arg('$cellBuilder', service('.inner'))
        ->arg('$permissionChecker', service(PropertyPermissionCheckerInterface::class))
    ;
    $services->set(DataCellViewBuilder::class)
        ->decorate(id: CellViewBuilderInterface::class, priority: 50)
        ->arg('$cellBuilder', service('.inner'))
        ->arg('$dataMapper', service(DataMapperInterface::class))
        ->arg('$transformerRegistry', service(DataTransformerRegistryInterface::class))
    ;
    $services->set(CompoundCellViewBuilder::class)
        ->decorate(id: CellViewBuilderInterface::class, priority: 25)
        ->arg('$cellBuilder', service('.inner'))
    ;
    $services->set(CollectionCellViewBuilder::class)
        ->decorate(id: CellViewBuilderInterface::class, priority: 150)
        ->arg('$cellBuilder', service('.inner'))
        ->arg('$dataMapper', service(DataMapperInterface::class))
    ;
    $services->set(ConditionCellViewBuilder::class)
        ->decorate(id: CellViewBuilderInterface::class, priority: 100)
        ->arg('$cellBuilder', service('.inner'))
        ->arg('$conditionMatcher', service(ConditionMatcherInterface::class))
    ;

    // Action view builder
    $services->set(ActionViewBuilderInterface::class, ActionViewBuilder::class)
        ->arg('$urlGenerator', service(UrlGeneratorInterface::class))
        ->arg('$conditionMatcher', service(ConditionMatcherInterface::class))
        ->arg('$translator', service('translator'))
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
    $services->set(EnumDataTransformer::class)
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
