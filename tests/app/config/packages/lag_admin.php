<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\LagAdminConfig;

return static function (LagAdminConfig $admin): void {
    $admin->mapping()
        ->paths([param('kernel.project_dir').'/src/Entity'])
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
