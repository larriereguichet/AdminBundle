<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Condition\Matcher\ConditionMatcherInterface;
use LAG\AdminBundle\Grid\DataTransformer\CountDataTransformer;
use LAG\AdminBundle\Grid\DataTransformer\EnumDataTransformer;
use LAG\AdminBundle\Grid\DataTransformer\FormDataTransformer;
use LAG\AdminBundle\Grid\Factory\GridFactory;
use LAG\AdminBundle\Grid\Factory\GridFactoryInterface;
use LAG\AdminBundle\Grid\Registry\DataTransformerRegistry;
use LAG\AdminBundle\Grid\Registry\DataTransformerRegistryInterface;
use LAG\AdminBundle\Grid\ViewBuilder\AttributeBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\AttributeBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\CellBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\CellBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\CollectionCellBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\CompoundCellBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\ConditionCellBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\DataCellBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\GridBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\GridBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\HeaderBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\HeaderBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\LinkBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\LinkBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\RowBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\RowBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\SecurityCellBuilder;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;
use LAG\AdminBundle\Routing\UrlGenerator\LinkUrlGeneratorInterface;
use LAG\AdminBundle\Security\PermissionChecker\PropertyPermissionCheckerInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Factories
    $services->set(GridFactoryInterface::class, GridFactory::class)
        ->args([
            '$metadataFactory' => service('lag_admin.grid.metadata_factory'),
            '$validator' => service('validator'),
        ])
        ->alias('lag_admin.grid.factory', GridFactoryInterface::class)
    ;

    // View builders
    $services->set(GridBuilderInterface::class, GridBuilder::class)
        ->args([
            '$rowBuilder' => service(RowBuilderInterface::class),
            '$attributeBuilder' => service(AttributeBuilderInterface::class),
        ])
        ->alias('lag_admin.grid.view_builder', GridBuilderInterface::class)
    ;
    $services->set(RowBuilderInterface::class, RowBuilder::class)
        ->arg('$cellBuilder', service(CellBuilderInterface::class))
        ->arg('$headerBuilder', service(HeaderBuilderInterface::class))
        ->arg('$linkBuilder', service(LinkBuilderInterface::class))
        ->arg('$attributeBuilder', service(AttributeBuilderInterface::class))
    ;
    $services->set(HeaderBuilderInterface::class, HeaderBuilder::class)
        ->args([
            '$attributeBuilder' => service(AttributeBuilderInterface::class),
        ])
    ;
    $services->set(CellBuilderInterface::class, CellBuilder::class)
        ->args([
            '$attributeBuilder' => service(AttributeBuilderInterface::class),
        ])
    ;
    $services->set(SecurityCellBuilder::class)
        ->decorate(id: CellBuilderInterface::class, priority: 200)
        ->arg('$cellBuilder', service('.inner'))
        ->arg('$permissionChecker', service(PropertyPermissionCheckerInterface::class))
        ->arg('$attributeBuilder', service(AttributeBuilderInterface::class))
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
        ->arg('$conditionMatcher', service(ConditionMatcherInterface::class))
        ->arg('$cellBuilder', service('.inner'))
        ->arg('$attributeBuilder', service(AttributeBuilderInterface::class))
    ;
    $services->set(LinkBuilderInterface::class, LinkBuilder::class)
        ->args([
            '$urlGenerator' => service(LinkUrlGeneratorInterface::class),
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
};
