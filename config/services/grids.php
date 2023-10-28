<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Grid\Factory\CellFactory;
use LAG\AdminBundle\Grid\Factory\CellFactoryInterface;
use LAG\AdminBundle\Grid\Factory\GridFactory;
use LAG\AdminBundle\Grid\Factory\GridFactoryInterface;
use LAG\AdminBundle\Grid\Registry\GridRegistry;
use LAG\AdminBundle\Grid\Registry\GridRegistryInterface;
use LAG\AdminBundle\Grid\View\CellRenderer;
use LAG\AdminBundle\Grid\View\CellRendererInterface;
use LAG\AdminBundle\Grid\View\GridRenderer;
use LAG\AdminBundle\Grid\View\GridRendererInterface;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Validation\Validator\GridExistValidator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(GridFactoryInterface::class, GridFactory::class)
        ->arg('$registry', service(GridRegistryInterface::class))
        ->arg('$cellFactory', service(CellFactoryInterface::class))
        ->arg('$eventDispatcher', service('event_dispatcher'))
    ;

    $services->set(CellFactoryInterface::class, CellFactory::class);

    $services->set(GridRendererInterface::class, GridRenderer::class)
        ->arg('$environment', service('twig'))
    ;

    $services->set(CellRendererInterface::class, CellRenderer::class)
        ->arg('$environment', service('twig'))
    ;

    $services->set(GridRegistryInterface::class, GridRegistry::class)
        ->arg('$validator', service('validator'))
        ->arg('$gridsConfiguration', '%lag_admin.grids%')
    ;

    $services->set(GridExistValidator::class)
        ->arg('$registry', service(ResourceRegistryInterface::class))
        ->tag('validator.constraint_validator')
    ;
};
