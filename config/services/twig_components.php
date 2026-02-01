<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\View\Component\Grid;
use LAG\AdminBundle\View\Component\GridActions;
use LAG\AdminBundle\View\Component\GridCell;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

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
    $services->set(GridActions::class)
        ->tag('twig.component', [
            'key' => 'lag_admin:grid_actions',
            'template' => '@LAGAdmin/components/grid_actions.html.twig',
            'expose_public_props' => true,
        ])
    ;
};
