<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Workflow\WorkflowInterface;

final class WorkflowInstalledValidator extends ConstraintValidator
{
    /** @param WorkflowInstalled $constraint */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        if (!\is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!interface_exists(WorkflowInterface::class)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
