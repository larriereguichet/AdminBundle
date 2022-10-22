<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Validation\Validator;

use LAG\AdminBundle\Metadata\AdminResource;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AdminValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$value instanceof AdminResource) {
            throw new UnexpectedTypeException($value, AdminResource::class);
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
