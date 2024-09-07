<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class WorkflowInstalled extends Constraint
{
    public string $message = 'The workflow component should be installed to use the workflow property. Try running composer require symfony/workflow';

    public function validatedBy(): string
    {
        return WorkflowInstalledValidator::class;
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
