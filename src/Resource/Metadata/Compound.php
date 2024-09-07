<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Compound extends Property implements CompoundPropertyInterface
{
    public function __construct(
        ?string $name = null,
        ?string $propertyPath = null,
        ?string $label = null,
        ?string $template = '@LAGAdmin/grids/properties/collection.html.twig',
        bool $sortable = true,
        bool $translatable = false,
        ?string $translationDomain = null,
        array $attributes = [],
        array $headerAttributes = [],
        ?string $dataTransformer = null,
        ?array $permissions = null,
        ?string $condition = null,

        private array $properties = [],
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
            dataTransformer: $dataTransformer,
            permissions: $permissions,
            condition: $condition,
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
