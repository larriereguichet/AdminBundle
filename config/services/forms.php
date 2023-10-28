<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Form\Extension\ChoiceTypeExtension;
use LAG\AdminBundle\Form\Extension\CollectionTypeExtension;
use LAG\AdminBundle\Form\Extension\TabResourceTypeExtension;
use LAG\AdminBundle\Form\Transformer\ImageFileToArrayTransformer;
use LAG\AdminBundle\Form\Type\DateRangeType;
use LAG\AdminBundle\Form\Type\Image\ImageChoiceType;
use LAG\AdminBundle\Form\Type\Image\ImageType;
use LAG\AdminBundle\Form\Type\Resource\FilterType;
use LAG\AdminBundle\Form\Type\Resource\ResourceChoiceType;
use LAG\AdminBundle\Form\Type\Resource\ResourceType;
use LAG\AdminBundle\Form\Type\Security\LoginType;
use LAG\AdminBundle\Form\Type\TinyMce\TinyMceType;
use LAG\AdminBundle\Metadata\Context\ResourceContextInterface;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\State\Provider\DataProviderInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Form types
    $services->set(TinyMceType::class)
        ->tag('form.type')
    ;

    $services->set(ResourceType::class)
        ->arg('$context', service(ResourceContextInterface::class))
        ->arg('$requestStack', service('request_stack'))
        ->tag('form.type')
    ;

    $services->set(FilterType::class)
        ->arg('$registry', service(ResourceRegistryInterface::class))
        ->tag('form.type')
    ;

    $services->set(DateRangeType::class)
        ->tag('form.type')
    ;

    $services->set(LoginType::class)
        ->arg('$configuration', service(ApplicationConfiguration::class))
        ->tag('form.type')
    ;

    $services->set(ImageType::class)
        ->tag('form.type')
    ;

    $services->set(ImageChoiceType::class)
        ->arg('$transformer', service(ImageFileToArrayTransformer::class))
        ->tag('form.type')
    ;

    $services->set(ResourceChoiceType::class)
        ->arg('$registry', service(ResourceRegistryInterface::class))
        ->arg('$dataProvider', service(DataProviderInterface::class))
        ->tag('form.type')
    ;

    // Form types extensions
    $services->set(CollectionTypeExtension::class)
        ->tag('form.type_extension')
    ;

    $services->set(ChoiceTypeExtension::class)
        ->tag('form.type_extension')
    ;

    $services->set(TabResourceTypeExtension::class)
        ->tag('form.type_extension')
    ;

    $services->set(ImageFileToArrayTransformer::class);
};
