<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class TemplateValid extends Constraint
{
    public function validatedBy(): string
    {
        return TemplateValidator::class;
    }
}
