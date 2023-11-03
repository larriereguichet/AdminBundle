<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class GridExist extends Constraint
{
    public function validatedBy(): string
    {
        return GridExistValidator::class;
    }
}
