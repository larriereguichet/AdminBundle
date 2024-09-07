<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Condition\Matcher\ConditionMatcher;
use LAG\AdminBundle\Condition\Matcher\ConditionMatcherInterface;
use LAG\AdminBundle\Condition\Matcher\ValidationConditionMatcher;
use LAG\AdminBundle\Condition\Matcher\WorkflowConditionMatcher;
use LAG\AdminBundle\Validation\Constraint\TemplateValidator;
use Symfony\Component\Workflow\Validator\WorkflowValidator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Validators
    $services->set(TemplateValidator::class)
        ->arg('$environment', service('twig'))
        ->tag('validator.constraint_validator')
    ;
    $services->set(WorkflowValidator::class)
        ->tag('validator.constraint_validator')
    ;

    // Condition matchers
    $services->set(ConditionMatcherInterface::class, ConditionMatcher::class)
        ->arg('$authorizationChecker', service('security.authorization_checker'))
    ;
    $services->set(WorkflowConditionMatcher::class)
        ->decorate(id: ConditionMatcherInterface::class, priority: 200)
        ->arg('$conditionMatcher', service('.inner'))
        ->arg('$workflowRegistry', service('workflow.registry'))
    ;
    $services->set(ValidationConditionMatcher::class)
        ->decorate(id: ConditionMatcherInterface::class, priority: 200)
        ->arg('$conditionMatcher', service('.inner'))
        ->arg('$validator', service('validator'))
    ;
};
