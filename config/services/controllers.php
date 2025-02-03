<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Controller\Resource\ProcessResource;
use LAG\AdminBundle\Controller\Resource\IndexResources;
use LAG\AdminBundle\Controller\Resource\ShowResource;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Grid\Registry\GridRegistryInterface;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilderInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(IndexResources::class)
        ->arg('$provider', service(ProviderInterface::class))
        ->arg('$processor', service(ProcessorInterface::class))
        ->arg('$gridRegistry', service(GridRegistryInterface::class))
        ->arg('$gridViewBuilder', service(GridViewBuilderInterface::class))
        ->arg('$formFactory', service('form.factory'))
        ->arg('$eventDispatcher', service(ResourceEventDispatcherInterface::class))
        ->arg('$responseHandler', service('lag_admin.response_handler'))
        ->tag('controller.service_arguments')
    ;
    $services->set(ProcessResource::class)
        ->arg('$provider', service(ProviderInterface::class))
        ->arg('$processor', service(ProcessorInterface::class))
        ->arg('$formFactory', service('form.factory'))
        ->arg('$eventDispatcher', service(ResourceEventDispatcherInterface::class))
        ->arg('$responseHandler', service('lag_admin.response_handler'))
        ->tag('controller.service_arguments')
    ;
    $services->set(ShowResource::class)
        ->arg('$provider', service(ProviderInterface::class))
        ->arg('$eventDispatcher', service(ResourceEventDispatcherInterface::class))
        ->arg('$responseHandler', service('lag_admin.response_handler'))
        ->tag('controller.service_arguments')
    ;
};
