<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Group extends Property
{
    public function __construct(
        ?string $name = null,
        ?string $label = null,
        private array $properties = [],
    ) {
        parent::__construct(
            name: $name,
            label: $label,
            template: '@LAGAdmin/grids/properties/group.html.twig',
            sortable: false,
            propertyPath: '.',
        );
    }

    public function getProperties(): array
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
