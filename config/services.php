<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->import(__DIR__.'/services/*.php');
    $container->import(__DIR__.'/services/bridges/*.php');
};
