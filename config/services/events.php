<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcher;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\EventListener\Data\PasswordListener;
use LAG\AdminBundle\EventListener\Data\SlugListener;
use LAG\AdminBundle\EventListener\Data\TimestampableListener;
use LAG\AdminBundle\EventListener\Data\UploadListener;
use LAG\AdminBundle\EventListener\Security\PermissionListener;
use LAG\AdminBundle\EventListener\View\DynamicUxComponentRenderListener;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;
use LAG\AdminBundle\Slug\Registry\SluggerRegistryInterface;
use LAG\AdminBundle\Upload\Uploader\UploaderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\UX\TwigComponent\Event\PreRenderEvent;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Security listeners
    $services->set(PermissionListener::class)
        ->args([
            '$parametersExtractor' => service(ParametersExtractorInterface::class),
            '$operationContext' => service('lag_admin.operation.context'),
            '$security' => service('security.helper'),
        ])
        ->tag('kernel.event_listener', ['event' => KernelEvents::REQUEST])
    ;

    // Data listeners
    $services->set(TimestampableListener::class)
        ->tag('kernel.event_listener', ['event' => 'lag_admin.resource.data_process', 'priority' => 250])
    ;
    $services->set(SlugListener::class)
        ->arg('$registry', service(SluggerRegistryInterface::class))
        ->tag('kernel.event_listener', ['event' => 'lag_admin.resource.data_process', 'priority' => 250])
    ;
    $services->set(UploadListener::class)
        ->arg('$dataMapper', service(DataMapperInterface::class))
        ->arg('$uploader', service(UploaderInterface::class))
        ->tag('kernel.event_listener', ['event' => 'lag_admin.resource.data_process', 'priority' => 250])
    ;
    $services->set(PasswordListener::class)
        ->arg('$passwordHasher', service('security.password_hasher'))
        ->tag('kernel.event_listener', ['event' => 'lag_admin.resource.data_process', 'priority' => 250])
    ;

    // View listeners
    $services->set(DynamicUxComponentRenderListener::class)
        ->tag('kernel.event_listener', ['event' => PreRenderEvent::class])
    ;

    // Dispatcher
    $services->set('lag_admin.build_event_dispatcher', EventDispatcher::class)
        ->tag('event_dispatcher.dispatcher', ['name' => 'lag_admin.build_event_dispatcher'])
    ;
    $services->set(ResourceEventDispatcherInterface::class, ResourceEventDispatcher::class)
        ->args([
            '$buildEventDispatcher' => service('lag_admin.build_event_dispatcher'),
            '$eventDispatcher' => service('event_dispatcher'),
        ])
        ->alias('lag_admin.event_dispatcher', ResourceEventDispatcherInterface::class)
    ;
};
