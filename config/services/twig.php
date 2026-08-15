<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Twig\Component\Cell\Cell;
use LAG\AdminBundle\Twig\Component\Grid;
use LAG\AdminBundle\Twig\Component\GridHeader;
use LAG\AdminBundle\Twig\Component\Links;
use LAG\AdminBundle\Twig\Component\Row;
use LAG\AdminBundle\Twig\Component\TableGrid;
use LAG\AdminBundle\Twig\Globals\LAGAdminGlobal;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Globals
    $services->set(LAGAdminGlobal::class)
        ->args([
            '$resourceContext' => service('lag_admin.resource.context'),
        ])
        ->alias('lag_admin.twig.global', LAGAdminGlobal::class)
    ;

    // Components
    $services->set(Grid::class)
        ->tag('twig.component', [
            'key' => 'lag_admin:grid',
            'template' => '@LAGAdmin/components/grid.html.twig',
            'expose_public_props' => true,
        ])
    ;
    $services->set(TableGrid::class)
        ->tag('twig.component', [
            'key' => 'lag:table_grid',
            'template' => '@LAGAdmin/components/table_grid.html.twig',
            'expose_public_props' => true,
        ])
    ;
    $services->set(Row::class)
        ->tag('twig.component', [
            'key' => 'lag_admin:row',
            'template' => '@LAGAdmin/components/row.html.twig',
            'expose_public_props' => true,
        ])
    ;
    $services->set(Cell::class)
        ->tag('twig.component', [
            'key' => 'lag_admin:cell',
            'template' => '@LAGAdmin/components/cell.html.twig',
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
    $services->set(GridHeader::class)
        ->tag('twig.component', [
            'key' => 'lag_admin:table_header',
            'template' => '@LAGAdmin/components/table_header.html.twig',
            'expose_public_props' => true,
        ])
    ;
};
