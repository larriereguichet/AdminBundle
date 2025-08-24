<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\View\Component\Grid;
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
    $services->set(GridCell::class)
        ->tag('twig.component', [
            'key' => 'lag_admin:grid:cell',
            'template' => '@LAGAdmin/components/cells/cell.html.twig',
            'expose_public_props' => true,
        ])
    ;
};
