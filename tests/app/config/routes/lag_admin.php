<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routing): void {
    $routing->import('@LAGAdminBundle/config/routing.php');
    $routing->import('@LAGAdminBundle/config/routing/security.php');
};
