<?php

namespace LAG\AdminBundle\Validation\Constraint;

use LAG\AdminBundle\Validation\Validator\TemplateValidator;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class TemplateValid extends Constraint
{
    public function validatedBy(): string
    {
        return TemplateValidator::class;
    }
}
