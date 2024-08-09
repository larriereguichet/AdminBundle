<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class TemplateValid extends Constraint
{
    public function __construct(
        public string $message = 'The template should be a valid Twig template',
        mixed $options = null,
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct($options, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return TemplateValidator::class;
    }
}
