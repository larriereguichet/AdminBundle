<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services
        ->set(ApplicationConfiguration::class)
        ->arg('$configuration', param('lag_admin.application.configuration'))
    ;
    $services->alias('lag_admin.application', ApplicationConfiguration::class);
};
