<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Filter\Factory\EventFilterFactory;
use LAG\AdminBundle\Filter\Factory\FilterFactory;
use LAG\AdminBundle\Filter\Factory\FilterFactoryInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(FilterFactoryInterface::class, FilterFactory::class)
        ->arg('$validator', service('validator'));

    $services->set(EventFilterFactory::class)
        ->decorate(FilterFactoryInterface::class)
        ->arg('$eventDispatcher', service('event_dispatcher'))
        ->arg('$decorated', service('.inner'));
};
