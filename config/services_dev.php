<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Debug\DataCollector\AdminDataCollector;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(AdminDataCollector::class)
        ->args([
            '$operationContext' => service('lag_admin.resource.context'),
        ])
        ->tag('data_collector', ['template' => '@LAGAdmin/debug/template.html.twig', 'id' => AdminDataCollector::class])
        ->private()
    ;
};
