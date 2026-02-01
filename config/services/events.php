<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcher;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\EventListener\Data\GeneratePasswordListener;
use LAG\AdminBundle\EventListener\Data\GenerateSlugListener;
use LAG\AdminBundle\EventListener\Data\GenerateTimestampListener;
use LAG\AdminBundle\EventListener\Data\UploadImageListener;
use LAG\AdminBundle\EventListener\Security\AccessListener;
use LAG\AdminBundle\EventListener\View\DynamicUxComponentRenderListener;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;
use LAG\AdminBundle\Slug\Registry\SluggerRegistryInterface;
use LAG\AdminBundle\Upload\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\UX\TwigComponent\Event\PreRenderEvent;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Security listeners
    $services->set(AccessListener::class)
        ->args([
            '$operationContext' => service('lag_admin.operation.context'),
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
    $services->set(DynamicUxComponentRenderListener::class)
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
