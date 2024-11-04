<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\LagAdminConfig;

return static function (LagAdminConfig $admin): void {
    $admin->resourcePaths([
        param('kernel.project_dir').'/src/Entity',
       // param('kernel.project_dir').'/config/resources',
    ])->gridPaths([
        param('kernel.project_dir').'/src/Entity',
        //param('kernel.project_dir').'/config/grids',
    ])
    ;
    $admin->applications('admin')
        ->translationPattern('{resource}.{message}')
        ->translationDomain('admin')
    ;
    $admin->applications('shop')
        ->translationPattern('{resource}.{message}')
        ->translationDomain('shop')
    ;
};
