<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Debug\DataCollector\AdminDataCollector;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

        $services->set(AdminDataCollector::class)
            ->args([
                '$operationContext' => service('lag_admin.operation.context'),
            ])
            ->tag('data_collector', ['template' => '@LAGAdmin/debug/template.html.twig', 'id' => AdminDataCollector::class])
            ->private()
        ;

    $services->set('lag_admin.debug.event_dispatcher', TraceableEventDispatcher::class)
        ->decorate('lag_admin.build_event_dispatcher')
        ->args([
            service('.inner'),
            service('debug.stopwatch'),
            service('logger')->nullOnInvalid(),
            service('.virtual_request_stack')->nullOnInvalid(),
        ])
    ;
};
