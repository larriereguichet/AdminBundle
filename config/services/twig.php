<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Twig\Globals\LAGAdminGlobal;
use LAG\AdminBundle\View\Component\Grid;
use LAG\AdminBundle\View\Component\GridCell;
use LAG\AdminBundle\View\Component\Links;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Globals
    $services->set(LAGAdminGlobal::class)
        ->args([
            '$applicationContext' => service('lag_admin.application.context'),
            '$resourceContext' => service('lag_admin.resource.context'),
            '$operationContext' => service('lag_admin.operation.context'),
        ])
        ->alias('lag_admin.twig.global', LAGAdminGlobal::class)
    ;

    // Components
    $services->set(Grid::class)
        ->tag('twig.component', [
            'key' => 'lag_admin:grid',
            'template' => '@LAGAdmin/components/grids/grid.html.twig',
            'expose_public_props' => true,
        ])
    ;
    $services->set(Grid::class)
        ->tag('twig.component', [
            'key' => 'lag_admin:templated_grid',
            'template' => '@LAGAdmin/components/templated_grid.html.twig',
            'expose_public_props' => true,
        ])
    ;
    $services->set(GridCell::class)
        ->tag('twig.component', [
            'key' => 'lag_admin:grid_cell',
            'template' => '@LAGAdmin/components/cells/cell.html.twig',
            'expose_public_props' => true,
        ])
    ;
    $services->set(Links::class)
        ->tag('twig.component', [
            'key' => 'lag_admin:links',
            'template' => '@LAGAdmin/components/links.html.twig',
            'expose_public_props' => true,
        ])
    ;
};
