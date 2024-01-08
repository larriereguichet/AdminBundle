<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

#[\Attribute]
class Collection extends Property implements CollectionPropertyInterface
{
    public function __construct(
        private PropertyInterface $propertyType,
        ?string $name = null,
        string $propertyPath = null,
        string $label = null,
        ?string $template = '@LAGAdmin/grids/properties/collection.html.twig',
        bool $sortable = true,
        bool $translatable = false,
        string $translationDomain = null,
        array $attributes = [],
        array $headerAttributes = [],
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: $template,
            sortable: $sortable,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attributes: $attributes,
            headerAttributes: $headerAttributes,
        );
    }

    public function getPropertyType(): PropertyInterface
    {
        return $this->propertyType;
    }

    public function withPropertyType(PropertyInterface $property): self
    {
        $self = clone $this;
        $self->propertyType = $property;

        return $self;
    }
}
