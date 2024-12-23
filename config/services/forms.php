<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Form\DataTransformer\TextareaDataTransformer;
use LAG\AdminBundle\Form\Extension\ChoiceTypeExtension;
use LAG\AdminBundle\Form\Extension\CollectionTypeExtension;
use LAG\AdminBundle\Form\Guesser\FormGuesser;
use LAG\AdminBundle\Form\Guesser\FormGuesserInterface;
use LAG\AdminBundle\Form\Type\Data\HiddenDataType;
use LAG\AdminBundle\Form\Type\DateRangeType;
use LAG\AdminBundle\Form\Type\Image\ImageType;
use LAG\AdminBundle\Form\Type\Resource\FilterType;
use LAG\AdminBundle\Form\Type\Resource\LegacyResourceType;
use LAG\AdminBundle\Form\Type\Resource\ResourceChoiceType;
use LAG\AdminBundle\Form\Type\Resource\ResourceDataType;
use LAG\AdminBundle\Form\Type\Security\LoginType;
use LAG\AdminBundle\Form\Type\Text\TextareaType;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Form types
    $services->set(LegacyResourceType::class)
        ->arg('$context', service(ResourceContextInterface::class))
        ->arg('$requestStack', service('request_stack'))
        ->tag('form.type')
    ;
    $services->set(FilterType::class)
        ->arg('$registry', service(ResourceRegistryInterface::class))
        ->tag('form.type')
    ;
    $services->set(DateRangeType::class)
        ->arg('$translationDomain', param('lag_admin.translation_domain'))
        ->tag('form.type')
    ;
    $services->set(LoginType::class)
        ->arg('$translationDomain', param('lag_admin.translation_domain'))
        ->tag('form.type')
    ;
    $services->set(ImageType::class)
        ->tag('form.type')
    ;
    $services->set(ResourceChoiceType::class)
        ->arg('$registry', service(ResourceRegistryInterface::class))
        ->arg('$dataProvider', service(ProviderInterface::class))
        ->tag('form.type')
    ;
    $services->set(HiddenDataType::class)
        ->arg('$defaultApplication', param('lag_admin.application_name'))
        ->arg('$registry', service(ResourceRegistryInterface::class))
        ->tag('form.type')
    ;
    $services->set(ResourceDataType::class)
        ->arg('$resourceRegistry', service(ResourceRegistryInterface::class))
        ->tag('form.type')
    ;
    $services->set(TextareaType::class)
        ->arg('$dataTransformer', service(TextareaDataTransformer::class))
        ->tag('form.type')
    ;

    // Form types extensions
    $services->set(CollectionTypeExtension::class)
        ->tag('form.type_extension')
    ;
    $services->set(ChoiceTypeExtension::class)
        ->tag('form.type_extension')
    ;

    // Form guessers
    $services->set(FormGuesserInterface::class, FormGuesser::class);

    // Data transformers
    $services->set(TextareaDataTransformer::class);
};
