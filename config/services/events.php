<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Event\GridEvents;
use LAG\AdminBundle\Event\OperationEvents;
use LAG\AdminBundle\Event\ResourceEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcher;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\EventListener\Data\PasswordListener;
use LAG\AdminBundle\EventListener\Data\SlugListener;
use LAG\AdminBundle\EventListener\Data\TimestampableListener;
use LAG\AdminBundle\EventListener\Data\UploadListener;
use LAG\AdminBundle\EventListener\Grid\InitializeGridListener;
use LAG\AdminBundle\EventListener\Operation\InitializeCollectionOperationFiltersListener;
use LAG\AdminBundle\EventListener\Operation\InitializeCollectionOperationListener;
use LAG\AdminBundle\EventListener\Operation\InitializeOperationListener;
use LAG\AdminBundle\EventListener\Operation\InitializeOperationPathListener;
use LAG\AdminBundle\EventListener\Operation\InitializeOperationRouteParametersListener;
use LAG\AdminBundle\EventListener\Resource\InitializeResourceListener;
use LAG\AdminBundle\EventListener\Resource\InitializeResourceOperationsListener;
use LAG\AdminBundle\EventListener\Resource\InitializeResourcePropertiesListener;
use LAG\AdminBundle\EventListener\Security\PermissionListener;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;
use LAG\AdminBundle\Resource\PropertyGuesser\ResourcePropertyGuesserInterface;
use LAG\AdminBundle\Resource\Registry\ApplicationRegistryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Slug\Registry\SluggerRegistryInterface;
use LAG\AdminBundle\Upload\Uploader\UploaderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\KernelEvents;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    // Resources listeners
    $services->set(InitializeResourceListener::class)
        ->arg('$application', param('lag_admin.application_name'))
        ->arg('$translationDomain', param('lag_admin.translation_domain'))
        ->arg('$registry', service(ApplicationRegistryInterface::class))
        ->tag('kernel.event_listener', [
            'event' => ResourceEvents::RESOURCE_CREATE,
            'dispatcher' => 'lag_admin.build_event_dispatcher',
            'priority' => 255,
        ])
    ;
    $services->set(InitializeResourceOperationsListener::class)
        ->arg('$routeNameGenerator', service(RouteNameGeneratorInterface::class))
        ->tag('kernel.event_listener', [
            'event' => ResourceEvents::RESOURCE_CREATE,
            'dispatcher' => 'lag_admin.build_event_dispatcher',
            'priority' => 254,
        ])
    ;
    $services->set(InitializeResourcePropertiesListener::class)
        ->arg('$propertyGuesser', service(ResourcePropertyGuesserInterface::class))
        ->tag('kernel.event_listener', [
            'event' => ResourceEvents::RESOURCE_CREATE,
            'dispatcher' => 'lag_admin.build_event_dispatcher',
            'priority' => 253,
        ])
    ;

    // Operations listeners
    $services->set(InitializeOperationListener::class)
        ->arg('$routeNameGenerator', service(RouteNameGeneratorInterface::class))
        ->arg('$applicationRegistry', service(ApplicationRegistryInterface::class))
        ->tag('kernel.event_listener', [
            'event' => OperationEvents::OPERATION_CREATE,
            'dispatcher' => 'lag_admin.build_event_dispatcher',
            'priority' => 255,
        ])
    ;
    $services->set(InitializeCollectionOperationListener::class)
        ->tag('kernel.event_listener', [
            'event' => OperationEvents::OPERATION_CREATE,
            'dispatcher' => 'lag_admin.build_event_dispatcher',
            'priority' => 254,
        ])
    ;
    $services->set(InitializeCollectionOperationFiltersListener::class)
        ->tag('kernel.event_listener', [
            'event' => OperationEvents::OPERATION_CREATE,
            'dispatcher' => 'lag_admin.build_event_dispatcher',
            'priority' => 253,
        ])
    ;
    $services->set(InitializeOperationPathListener::class)
        ->tag('kernel.event_listener', [
            'event' => OperationEvents::OPERATION_CREATE,
            'dispatcher' => 'lag_admin.build_event_dispatcher',
            'priority' => -255,
        ])
    ;
    $services->set(InitializeOperationRouteParametersListener::class)
        ->tag('kernel.event_listener', [
            'event' => OperationEvents::OPERATION_CREATE,
            'dispatcher' => 'lag_admin.build_event_dispatcher',
            'priority' => -255,
        ])
    ;

    // Grid listeners
    $services->set(InitializeGridListener::class)
        ->arg('$requestStack', service('request_stack'))
        ->tag('kernel.event_listener', [
            'event' => GridEvents::GRID_BUILD,
            'priority' => -255,
        ])
    ;

    // Security listeners
    $services->set(PermissionListener::class)
        ->arg('$resourceContext', service(ResourceContextInterface::class))
        ->arg('$security', service('security.helper'))
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

    // Dispatcher
    $services->set('lag_admin.build_event_dispatcher', EventDispatcher::class)
        ->tag('event_dispatcher.dispatcher', ['name' => 'lag_admin.build_event_dispatcher'])
    ;

    $services->set(ResourceEventDispatcherInterface::class, ResourceEventDispatcher::class)
        ->arg('$buildEventDispatcher', service('lag_admin.build_event_dispatcher'))
        ->arg('$eventDispatcher', service('event_dispatcher'))
    ;
    $services->alias('lag_admin.event_dispatcher', ResourceEventDispatcherInterface::class);
};
