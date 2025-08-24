<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Form\DataTransformer\TextareaDataTransformer;
use LAG\AdminBundle\Form\Extension\ChoiceTypeExtension;
use LAG\AdminBundle\Form\Extension\CollectionTypeExtension;
use LAG\AdminBundle\Form\Guesser\FormGuesser;
use LAG\AdminBundle\Form\Guesser\FormGuesserInterface;
use LAG\AdminBundle\Form\Type\Date\DateRangeType;
use LAG\AdminBundle\Form\Type\Image\ImageType;
use LAG\AdminBundle\Form\Type\Resource\FilterType;
use LAG\AdminBundle\Form\Type\Resource\ResourceDataChoiceType;
use LAG\AdminBundle\Form\Type\Resource\ResourceDataType;
use LAG\AdminBundle\Form\Type\Security\LoginType;
use LAG\AdminBundle\Form\Type\Text\TextareaType;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Form types
    $services->set(FilterType::class)
        ->args([
            '$operationFactory' => service('lag_admin.operation.factory'),
        ])
        ->tag('form.type')
    ;
    $services->set(DateRangeType::class)
        ->tag('form.type')
    ;
    $services->set(LoginType::class)
        ->tag('form.type')
    ;
    $services->set(ImageType::class)
        ->tag('form.type')
    ;
    $services->set(ResourceDataChoiceType::class)
        ->arg('$operationFactory', service('lag_admin.operation.factory'))
        ->arg('$dataProvider', service(ProviderInterface::class))
        ->tag('form.type')
    ;
    $services->set(ResourceDataType::class)
        ->arg('$operationFactory', service(OperationFactoryInterface::class))
        ->arg('$formGuesser', service(FormGuesserInterface::class))
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
