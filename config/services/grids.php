<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Condition\Matcher\ConditionMatcherInterface;
use LAG\AdminBundle\Grid\Factory\CacheGridFactory;
use LAG\AdminBundle\Grid\Factory\GridFactory;
use LAG\AdminBundle\Grid\Factory\GridFactoryInterface;
use LAG\AdminBundle\Grid\ViewBuilder\CellBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\CellBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\AttributeBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\AttributeBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\RowViewBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\RowViewBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\SecurityCellBuilder;
use LAG\AdminBundle\Metadata\DataTransformer\CountDataTransformer;
use LAG\AdminBundle\Metadata\DataTransformer\EnumDataTransformer;
use LAG\AdminBundle\Metadata\DataTransformer\FormDataTransformer;
use LAG\AdminBundle\Metadata\DataTransformer\MapDataTransformer;
use LAG\AdminBundle\Grid\Initializer\GridInitializer;
use LAG\AdminBundle\Grid\Initializer\GridInitializerInterface;
use LAG\AdminBundle\Metadata\Registry\DataTransformerRegistry;
use LAG\AdminBundle\Metadata\Registry\DataTransformerRegistryInterface;
use LAG\AdminBundle\Grid\ViewBuilder\ActionBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\ActionBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilderInterface;
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
            '$actionBuilder' => service(ActionBuilderInterface::class),
        ])
        ->alias('lag_admin.grid.view_builder', GridViewBuilderInterface::class)
    ;
    $services->set(RowViewBuilderInterface::class, RowViewBuilder::class)
        ->arg('$cellBuilder', service(CellBuilderInterface::class))
        ->arg('$actionsBuilder', service(ActionBuilderInterface::class))
        ->arg('$attributeBuilder', service(AttributeBuilderInterface::class))
    ;
    $services->set(HeaderViewBuilderInterface::class, HeaderViewBuilder::class);
    $services->set(SecurityHeaderViewBuilder::class)
        ->decorate(id: HeaderViewBuilderInterface::class, priority: 200)
        ->arg('$headerBuilder', service('.inner'))
        ->arg('$permissionChecker', service(PropertyPermissionCheckerInterface::class))
    ;
    $services->set(CellBuilderInterface::class, CellBuilder::class);
    $services->set(SecurityCellBuilder::class)
        ->decorate(id: CellBuilderInterface::class, priority: 200)
        ->arg('$cellBuilder', service('.inner'))
        ->arg('$permissionChecker', service(PropertyPermissionCheckerInterface::class))
    ;
    $services->set(DataCellBuilder::class)
        ->decorate(id: CellBuilderInterface::class, priority: 50)
        ->arg('$cellBuilder', service('.inner'))
        ->arg('$dataMapper', service(DataMapperInterface::class))
        ->arg('$transformerRegistry', service(DataTransformerRegistryInterface::class))
    ;
    $services->set(CompoundCellBuilder::class)
        ->decorate(id: CellBuilderInterface::class, priority: 25)
        ->arg('$cellBuilder', service('.inner'))
    ;
    $services->set(CollectionCellBuilder::class)
        ->decorate(id: CellBuilderInterface::class, priority: 150)
        ->arg('$cellBuilder', service('.inner'))
        ->arg('$dataMapper', service(DataMapperInterface::class))
    ;
    $services->set(ConditionCellBuilder::class)
        ->decorate(id: CellBuilderInterface::class, priority: 100)
        ->arg('$cellBuilder', service('.inner'))
        ->arg('$conditionMatcher', service(ConditionMatcherInterface::class))
    ;
    $services->set(ActionBuilderInterface::class, ActionBuilder::class)
        ->args([
            '$urlGenerator' => service(ResourceUrlGeneratorInterface::class),
            '$conditionMatcher' => service(ConditionMatcherInterface::class),
            '$translator' => service('translator'),
            '$attributeBuilder' => service(AttributeBuilderInterface::class),
        ])

    ;
    $services->set(AttributeBuilderInterface::class, AttributeBuilder::class)
        ->args(['$environment' => service('twig')])
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
