<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Controller\Resource\IndexResources;
use LAG\AdminBundle\Controller\Resource\ProcessResource;
use LAG\AdminBundle\Controller\Resource\ShowResource;
use LAG\AdminBundle\Controller\Security\Login;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(IndexResources::class)
        ->args([
            '$contextBuilder' => service('lag_admin.request.context_builder'),
            '$provider' => service(ProviderInterface::class),
            '$processor' => service(ProcessorInterface::class),
            '$gridBuilder' => service('lag_admin.grid.view_builder'),
            '$formFactory' => service('form.factory'),
            '$eventDispatcher' => service('lag_admin.event_dispatcher'),
            '$responseHandler' => service('lag_admin.response_handler'),
        ])
        ->tag('controller.service_arguments')
    ;
    $services->set(ProcessResource::class)
        ->args([
            '$contextBuilder' => service('lag_admin.request.context_builder'),
            '$provider' => service(ProviderInterface::class),
            '$processor' => service(ProcessorInterface::class),
            '$formFactory' => service('form.factory'),
            '$eventDispatcher' => service('lag_admin.event_dispatcher'),
            '$responseHandler' => service('lag_admin.response_handler'),
        ])
        ->tag('controller.service_arguments')
    ;
    $services->set(ShowResource::class)
        ->arg('$contextBuilder', service('lag_admin.request.context_builder'))
        ->arg('$provider', service(ProviderInterface::class))
        ->arg('$eventDispatcher', service(ResourceEventDispatcherInterface::class))
        ->arg('$responseHandler', service('lag_admin.response_handler'))
        ->tag('controller.service_arguments')
    ;
    $services->set(Login::class)
        ->arg('$authenticationUtils', service('security.authentication_utils'))
        ->arg('$environment', service('twig'))
        ->tag('controller.service_arguments')
    ;
};
