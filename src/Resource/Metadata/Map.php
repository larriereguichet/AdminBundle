<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Map extends Property
{
    public function __construct(
        ?string $name = null,
        ?string $propertyPath = null,
        ?string $label = null,
        bool $sortable = true,
        bool $translatable = true,
        ?string $translationDomain = null,
        array $attributes = [],
        array $headerAttributes = [],
        ?string $dataTransformer = null,
        ?array $permissions = null,
        ?string $condition = null,
        ?string $sortingPath = null,

        #[Assert\Count(min: 1, minMessage: 'The map should have at least 1 element')]
        private array $map = [],
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: '@LAGAdmin/grids/properties/map.html.twig',
            sortable: $sortable,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attributes: $attributes,
            headerAttributes: $headerAttributes,
            dataTransformer: $dataTransformer,
            permissions: $permissions,
            condition: $condition,
            sortingPath: $sortingPath,
        );
    }

    public function getMap(): array
    {
        return $this->map;
    }

    public function withMap(array $map): self
    {
        $self = clone $this;
        $self->map = $map;

        return $self;
    }
}
