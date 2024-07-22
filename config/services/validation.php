<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Condition\ConditionMatcher;
use LAG\AdminBundle\Condition\ConditionMatcherInterface;
use LAG\AdminBundle\Validation\Constraint\TemplateValidator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(TemplateValidator::class)
        ->arg('$environment', service('twig'))
        ->tag('validator.constraint_validator')
    ;

    $services->set(ConditionMatcherInterface::class, ConditionMatcher::class)
        ->arg('$workflowRegistry', service('workflow.registry'))
    ;
};
