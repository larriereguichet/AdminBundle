<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Controller\Security\Login;
use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->add('lag_admin.login', '/login')
        ->controller(Login::class)
    ;
    $routingConfigurator->add('lag_admin.login_check', '/login-check');
    $routingConfigurator->add('lag_admin.logout', '/logout');
};
