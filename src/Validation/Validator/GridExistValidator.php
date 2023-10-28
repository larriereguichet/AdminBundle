<?php

namespace LAG\AdminBundle\Validation\Validator;

use LAG\AdminBundle\Grid\Registry\GridRegistryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class GridExistValidator extends ConstraintValidator
{
    public function __construct(
        private GridRegistryInterface $registry,
    )
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!$this->registry->has($value)) {
            $this->context->addViolation(sprintf('The grid "%s" does not exists', $value));
        }
    }
}
