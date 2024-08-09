<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->add('lag_admin.homepage', '/')
        ->controller(TemplateController::class)
        ->defaults(['template' => '@LAGAdmin/pages/home.html.twig', 'priority' => -255])
    ;
};
