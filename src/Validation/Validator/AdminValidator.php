<?php

namespace LAG\AdminBundle\Validation\Validator;

use LAG\AdminBundle\Metadata\Admin;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AdminValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$value instanceof Admin) {
            throw new UnexpectedTypeException($value, Admin::class);
        }

        if (!$value->getName()) {
            $this->context->addViolation('The admin has an empty name');
        }

        if (!$value->getTitle()) {
            $this->context->addViolation('The admin has an empty title');
        }

        if (!class_exists($value->getDataClass())) {
            $this->context->addViolation(sprintf(
                'The admin "%s" has an invalid data class "%s"',
                $value->getName(),
                $value->getDataClass(),
            ));
        }
    }
}
