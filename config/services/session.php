<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Session\FlashMessageHelper;
use LAG\AdminBundle\Session\FlashMessageHelperInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(FlashMessageHelperInterface::class, FlashMessageHelper::class)
        ->arg('$requestStack', service('request_stack'))
    ;
};
