<?php

namespace LAG\AdminBundle\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class AtLeastOneIdentifier extends Constraint
{
    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT];
    }

    public function validatedBy(): string
    {
        return AtLeastOneIdentifierValidator::class;
    }
}
