<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Security\PermissionChecker\PropertyPermissionChecker;
use LAG\AdminBundle\Security\PermissionChecker\PropertyPermissionCheckerInterface;
use LAG\AdminBundle\Security\Voter\OperationVoter;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(PropertyPermissionCheckerInterface::class, PropertyPermissionChecker::class)
        ->arg('$security', service('security.helper'))
    ;

    $services->set(OperationVoter::class)
        ->arg('$security', service('security.helper'))
        ->tag('security.voter')
    ;
};
