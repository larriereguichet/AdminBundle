<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Event\DataEvents;
use LAG\AdminBundle\Event\GridEvents;
use LAG\AdminBundle\Event\OperationEvents;
use LAG\AdminBundle\Event\ResourceEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcher;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\EventListener\Data\SlugListener;
use LAG\AdminBundle\EventListener\Data\TimestampableListener;
use LAG\AdminBundle\EventListener\Grid\InitializeGridListener;
use LAG\AdminBundle\EventListener\Operation\InitializeCollectionOperationListener;
use LAG\AdminBundle\EventListener\Operation\InitializeOperationListener;
use LAG\AdminBundle\EventListener\Operation\OperationPathListener;
use LAG\AdminBundle\EventListener\Resource\InitializeResourceListener;
use LAG\AdminBundle\EventListener\Resource\InitializeResourceOperationsListener;
use LAG\AdminBundle\EventListener\Resource\InitializeResourcePropertiesListener;
use LAG\AdminBundle\EventListener\Security\PermissionListener;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Slug\Generator\SlugGeneratorInterface;
use LAG\AdminBundle\Slug\Mapping\SlugMappingInterface;
use Symfony\Component\HttpKernel\KernelEvents;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    // Resources listeners
    $services->set(InitializeResourceListener::class)
        ->arg('$applicationName', param('lag_admin.application_name'))
        ->arg('$translationDomain', param('lag_admin.translation_domain'))
        ->tag('kernel.event_listener', ['event' => 'lag_admin.resource.create', 'priority' => 255])
    ;
    $services->set(InitializeResourceOperationsListener::class)
        ->arg('$routeNameGenerator', service(RouteNameGeneratorInterface::class))
        ->tag('kernel.event_listener', ['event' => 'lag_admin.resource.create', 'priority' => 254])
    ;
    $services->set(InitializeResourcePropertiesListener::class)
        ->tag('kernel.event_listener', ['event' => 'lag_admin.resource.create', 'priority' => 253])
    ;

    // Operations listeners
    $services->set(InitializeOperationListener::class)
        ->arg('$routeNameGenerator', service(RouteNameGeneratorInterface::class))
        ->tag('kernel.event_listener', ['event' => 'lag_admin.operation.create', 'priority' => 255])
    ;
    $services->set(InitializeCollectionOperationListener::class)
        ->tag('kernel.event_listener', ['event' => 'lag_admin.operation.create', 'priority' => 254])
    ;
    $services->set(OperationPathListener::class)
        ->tag('kernel.event_listener', ['event' => 'lag_admin.operation.create', 'priority' => -255])
    ;
    $services->set(InitializeGridListener::class)
        ->tag('kernel.event_listener', ['event' => 'lag_admin.grid.create', 'priority' => -255])
    ;


    // Security listeners
    $services->set(PermissionListener::class)
        ->arg('$resourceContext', service(ResourceContextInterface::class))
        ->arg('$security', service('security.helper'))
        ->tag('kernel.event_listener', ['event' => KernelEvents::REQUEST])
    ;

    // Data listeners
    $services->set(TimestampableListener::class)
        ->tag('kernel.event_listener', ['event' => DataEvents::DATA_PROCESS, 'priority' => 255])
    ;
    $services->set(SlugListener::class)
        ->arg('$generator', service(SlugGeneratorInterface::class))
        ->arg('$mapping', service(SlugMappingInterface::class))
        ->tag('kernel.event_listener', ['event' => DataEvents::DATA_PROCESS, 'priority' => 255])
    ;

    // Dispatcher
    $services->set(ResourceEventDispatcherInterface::class, ResourceEventDispatcher::class)
        ->arg('$eventDispatcher', service('event_dispatcher'))
    ;
    $services->alias('lag_admin.event_dispatcher', ResourceEventDispatcherInterface::class);
};
