<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcher;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\EventListener\Data\GeneratePasswordListener;
use LAG\AdminBundle\EventListener\Data\GenerateSlugListener;
use LAG\AdminBundle\EventListener\Data\GenerateTimestampListener;
use LAG\AdminBundle\EventListener\Data\UploadImageListener;
use LAG\AdminBundle\EventListener\Resource\InitializeResourceContextListener;
use LAG\AdminBundle\EventListener\Security\AccessListener;
use LAG\AdminBundle\EventListener\View\AttributeComponentRenderListener;
use LAG\AdminBundle\EventListener\View\TemplateComponentRenderListener;
use LAG\AdminBundle\Upload\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\UX\TwigComponent\Event\PreRenderEvent;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Kernel listeners
    $services->set(InitializeResourceContextListener::class)
        ->args([
            '$requestParameter' => param('lag_admin.request_parameter'),
            '$resourceContext' => service('lag_admin.resource.context'),
            '$resourceFactory' => service('lag_admin.resource.factory'),
        ])
        ->tag('kernel.event_listener', ['event' => KernelEvents::REQUEST, 'method' => 'onRequest', 'priority' => -255])
        ->tag('kernel.event_listener', ['event' => KernelEvents::FINISH_REQUEST, 'method' => 'onFinishRequest', 'priority' => -255])
    ;
    // Security listeners
    $services->set(AccessListener::class)
        ->args([
            '$resourceContext' => service('lag_admin.resource.context'),
            '$security' => service('security.helper'),
        ])
        ->tag('kernel.event_listener', ['event' => KernelEvents::REQUEST])
    ;

    // Data listeners
    $services->set(GenerateTimestampListener::class)
        ->tag('kernel.event_listener', ['event' => 'lag_admin.resource.data_process', 'priority' => 250])
    ;
    $services->set(GenerateSlugListener::class)
        ->arg('$slugger', service('lag_admin.slugger'))
        ->tag('kernel.event_listener', ['event' => 'lag_admin.resource.data_process', 'priority' => 250])
    ;
    $services->set(UploadImageListener::class)
        ->arg('$uploader', service(ImageUploaderInterface::class))
        ->tag('kernel.event_listener', ['event' => 'lag_admin.resource.data_process', 'priority' => 250])
    ;
    $services->set(GeneratePasswordListener::class)
        ->arg('$passwordHasher', service('security.password_hasher'))
        ->tag('kernel.event_listener', ['event' => 'lag_admin.resource.data_process', 'priority' => 250])
    ;

    // View listeners
    $services->set(TemplateComponentRenderListener::class)
        ->tag('kernel.event_listener', ['event' => PreRenderEvent::class])
    ;
    $services->set(AttributeComponentRenderListener::class)
        ->tag('kernel.event_listener', ['event' => PreRenderEvent::class])
    ;

    // Dispatcher
    $services->set(ResourceEventDispatcherInterface::class, ResourceEventDispatcher::class)
        ->args([
            '$eventDispatcher' => service('event_dispatcher'),
        ])
        ->alias('lag_admin.event_dispatcher', ResourceEventDispatcherInterface::class)
    ;
};
