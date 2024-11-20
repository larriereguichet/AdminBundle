<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Controller\Resource\ResourceCollectionController;
use LAG\AdminBundle\Controller\Resource\ResourceController;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Grid\Registry\GridRegistryInterface;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilderInterface;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(ResourceController::class)
        ->arg('$uriVariablesExtractor', service(UriVariablesExtractorInterface::class))
        ->arg('$contextProvider', service(ContextProviderInterface::class))
        ->arg('$provider', service(ProviderInterface::class))
        ->arg('$processor', service(ProcessorInterface::class))
        ->arg('$formFactory', service('form.factory'))
        ->arg('$eventDispatcher', service(ResourceEventDispatcherInterface::class))
        ->arg('$responseHandler', service('lag_admin.response_handler'))

        ->tag('controller.service_arguments')
    ;

    $services->set(ResourceCollectionController::class)
        ->arg('$uriVariablesExtractor', service(UriVariablesExtractorInterface::class))
        ->arg('$contextProvider', service(ContextProviderInterface::class))
        ->arg('$provider', service(ProviderInterface::class))
        ->arg('$processor', service(ProcessorInterface::class))
        ->arg('$gridRegistry', service(GridRegistryInterface::class))
        ->arg('$gridViewBuilder', service(GridViewBuilderInterface::class))
        ->arg('$formFactory', service('form.factory'))
        ->arg('$eventDispatcher', service(ResourceEventDispatcherInterface::class))
        ->arg('$responseHandler', service('lag_admin.response_handler'))

        ->tag('controller.service_arguments')
    ;
};
