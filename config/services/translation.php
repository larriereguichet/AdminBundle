<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Translation\FallbackTranslator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(FallbackTranslator::class)
        ->decorate('translator')
        ->arg('$decorated', service('.inner'))
    ;
};
