<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Attribute;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

class TextFilter extends Filter
{
    /**
     * @param array<string, mixed> $formOptions
     * @param array<string>|null $properties
     */
    public function __construct(
        string $name,
        string $comparator = 'like',
        string $operator = 'and',
        string $formType = TextType::class,
        array $formOptions = [],

        #[Assert\Count(min: 1)]
        #[Assert\All(constraints: [new Assert\Type(type: 'string'), new Assert\NotBlank()])]
        private ?array $properties = null,
    ) {
        parent::__construct(
            name: $name,
            comparator: $comparator,
            operator: $operator,
            formType: $formType,
            formOptions: $formOptions,
        );
    }

    /** @return array<string, string>|null */
    public function getProperties(): ?array
    {
        return $this->properties;
    }

    /** @param array<string, string> $properties */
    public function withProperties(array $properties): self
    {
        $self = clone $this;
        $self->properties = $properties;

        return $self;
    }
}
