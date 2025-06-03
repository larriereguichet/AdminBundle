<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Controller\Security\Login;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->add('lag_admin_login', '/login')->controller(Login::class);
    $routingConfigurator->add('lag_admin_login_check', '/login-check');
    $routingConfigurator->add('lag_admin_logout', '/logout');
};
