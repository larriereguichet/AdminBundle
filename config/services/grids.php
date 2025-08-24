<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Condition\Matcher\ConditionMatcherInterface;
use LAG\AdminBundle\Grid\DataTransformer\CountDataTransformer;
use LAG\AdminBundle\Grid\DataTransformer\EnumDataTransformer;
use LAG\AdminBundle\Grid\DataTransformer\FormDataTransformer;
use LAG\AdminBundle\Grid\DataTransformer\MapDataTransformer;
use LAG\AdminBundle\Grid\Factory\CacheGridFactory;
use LAG\AdminBundle\Grid\Factory\GridFactory;
use LAG\AdminBundle\Grid\Factory\GridFactoryInterface;
use LAG\AdminBundle\Grid\Initializer\GridInitializer;
use LAG\AdminBundle\Grid\Initializer\GridInitializerInterface;
use LAG\AdminBundle\Grid\Registry\DataTransformerRegistry;
use LAG\AdminBundle\Grid\Registry\DataTransformerRegistryInterface;
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
use LAG\AdminBundle\Resource\Initializer\ActionInitializerInterface;
use LAG\AdminBundle\Routing\UrlGenerator\ResourceUrlGeneratorInterface;
use LAG\AdminBundle\Security\PermissionChecker\PropertyPermissionCheckerInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Factories
    $services->set(GridFactoryInterface::class, GridFactory::class)
        ->args([
            '$definitionFactory' => service('lag_admin.definition.factory'),
            '$gridInitializer' => service(GridInitializerInterface::class),
            '$validator' => service('validator'),
            '$builders' => tagged_iterator('lag_admin.grid_provider'),
        ])
        ->alias('lag_admin.grid.factory', GridFactoryInterface::class)
    ;
    $services->set(CacheGridFactory::class)
        ->decorate(GridFactoryInterface::class)
        ->args([
            '$gridFactory' => service('.inner'),
        ])
    ;

    // View builders
    $services->set(GridViewBuilderInterface::class, GridViewBuilder::class)
        ->args([
            '$gridFactory' => service('lag_admin.grid.factory'),
            '$rowBuilder' => service(RowViewBuilderInterface::class),
            '$actionBuilder' => service(ActionViewBuilderInterface::class),
        ])
        ->alias('lag_admin.grid.view_builder', GridViewBuilderInterface::class)
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
        ->arg('$urlGenerator', service(ResourceUrlGeneratorInterface::class))
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

    // Registry
    $services->set(DataTransformerRegistryInterface::class, DataTransformerRegistry::class)
        ->arg('$dataTransformers', tagged_iterator('lag_admin.data_transformer'))
    ;

    // Initializer
    $services->set(GridInitializerInterface::class, GridInitializer::class)
        ->args([
            '$requestStack' => service('request_stack'),
            '$actionInitializer' => service(ActionInitializerInterface::class),
            '$gridTemplates' => param('lag_admin.grids_templates'),
        ])
    ;
};
