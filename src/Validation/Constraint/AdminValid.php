<?php

namespace LAG\AdminBundle\Validation\Constraint;

use LAG\AdminBundle\Validation\Validator\AdminValidator;
use Symfony\Component\Validator\Constraint;

class AdminValid extends Constraint
{
    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT];
    }

    public function validatedBy(): string
    {
        return AdminValidator::class;
    }
}