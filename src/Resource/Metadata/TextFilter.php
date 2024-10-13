<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

class TextFilter extends Filter
{
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

    public function getProperties(): ?array
    {
        return $this->properties;
    }

    public function withProperties(array $properties): self
    {
        $self = clone $this;
        $self->properties = $properties;

        return $self;
    }
}
