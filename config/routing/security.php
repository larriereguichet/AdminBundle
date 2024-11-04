<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Controller\Security\Login;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routing): void {
    $routing->add('lag_admin.login', '/login')
        ->controller(Login::class)
    ;
    $routing->add('lag_admin.login_check', '/login-check');
    $routing->add('lag_admin.logout', '/logout');
};
