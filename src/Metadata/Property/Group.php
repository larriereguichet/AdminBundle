<?php

namespace LAG\AdminBundle\Metadata\Property;

class Group extends Property implements CompositePropertyInterface
{
    public function __construct(
        ?string $name = null,
        ?string $label = null,
        private array $children = [],
    ) {
        parent::__construct(
            name: $name,
            label: $label,
            template: '@LAGAdmin/grids/properties/group.html.twig',
            sortable: false,
            propertyPath: '.',
        );
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function withChildren(array $children): self
    {
        $self = clone $this;
        $self->children = $children;

        return $self;
    }
}
