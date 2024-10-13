<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Validation\Constraint;

use LAG\AdminBundle\Resource\Metadata\Delete;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Update;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class AtLeastOneIdentifierValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof OperationInterface) {
            throw new UnexpectedTypeException($value, OperationInterface::class);
        }

        if (!$value instanceof Update && !$value instanceof Delete) {
            return;
        }

        if ($value->getIdentifiers() === null || \count($value->getIdentifiers()) === 0) {
            $this->context
                ->buildViolation('The operation should have at least one identifier')
                ->atPath('identifiers')
                ->addViolation()
            ;
        }
    }
}
