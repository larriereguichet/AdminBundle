<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Validation\Validator\TemplateValidator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(TemplateValidator::class)
        ->arg('$environment', service('twig'))
        ->tag('validator.constraint_validator')
    ;
};
