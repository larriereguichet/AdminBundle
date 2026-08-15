<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Attribute;

use LAG\AdminBundle\Metadata\CompoundPropertyInterface;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Group extends Property implements CompoundPropertyInterface
{
    /** @param array<int|string, string> $properties */
    public function __construct(
        ?string $name = null,
        string|bool|null $propertyPath = true,
        string|bool|null $label = null,
        ?string $template = '@LAGAdmin/grids/properties/group.html.twig',
        bool $translatable = false,

        array $attributes = [],
        array $rowAttributes = [],
        array $headerAttributes = [],
        ?string $dataTransformer = null,
        ?array $permissions = null,
        ?string $condition = null,
        ?string $component = null,
        ?string $translationDomain = null,

        private array $properties = [],
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: $template,
            sortable: false,
            translatable: $translatable,

            attributes: $attributes,
            rowAttributes: $rowAttributes,
            headerAttributes: $headerAttributes,
            dataTransformer: $dataTransformer,
            permissions: $permissions,
            condition: $condition,
            component: $component,
            translationDomain: $translationDomain,
        );
    }

    /** @return array<int|string, string> */
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
