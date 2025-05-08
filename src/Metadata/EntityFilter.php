<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints as Assert;

final class EntityFilter extends Filter
{
    public function __construct(
        string $name,
        string $comparator = '=',
        string $operator = 'and',
        string $formType = EntityType::class,
        array $formOptions = [],

        #[Assert\NotNull(message: 'A property to filter should be defined')]
        private ?string $property = null,

        private bool $multiple = false,
    ) {
        parent::__construct(
            name: $name,
            comparator: $comparator,
            operator: $operator,
            formType: $formType,
            formOptions: $formOptions,
        );
    }

    public function getProperty(): ?string
    {
        return $this->property;
    }

    public function withProperty(string $property): self
    {
        $self = clone $this;
        $self->property = $property;

        return $self;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public function withMultiple(bool $multiple): self
    {
        $self = clone $this;
        $self->multiple = $multiple;

        return $self;
    }
}
