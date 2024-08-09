<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Twig\Environment;

class TemplateValidator extends ConstraintValidator
{
    public function __construct(
        private Environment $environment,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        if (!\is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!$this->environment->getLoader()->exists($value)) {
            $this
                ->context
                ->addViolation(\sprintf('The twig template "%s" does not exists', $value))
            ;
        }
    }
}
