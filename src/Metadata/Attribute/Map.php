<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Attribute;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Map extends Property
{
    /** @param array<int|string, mixed> $map */
    public function __construct(
        ?string $name = null,
        string|bool|null $propertyPath = null,
        string|bool|null $label = null,
        ?string $template = '@LAGAdmin/grids/properties/map.html.twig',
        bool $sortable = true,
        bool $translatable = true,

        array $attributes = [],
        array $rowAttributes = [],
        array $headerAttributes = [],
        ?string $dataTransformer = null,
        ?array $permissions = null,
        ?string $condition = null,
        ?string $sortingPath = null,
        ?string $component = null,
        ?string $translationDomain = null,

        #[Assert\Count(min: 1, minMessage: 'The map should have at least 1 element')]
        private array $map = [],
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: $template,
            sortable: $sortable,
            translatable: $translatable,

            attributes: $attributes,
            rowAttributes: $rowAttributes,
            headerAttributes: $headerAttributes,
            dataTransformer: $dataTransformer,
            permissions: $permissions,
            condition: $condition,
            sortingPath: $sortingPath,
            component: $component,
            translationDomain: $translationDomain,
        );
    }

    /** @return array<int|string, mixed> */
    public function getMap(): array
    {
        return $this->map;
    }

    /** @param array<int|string, mixed> $map */
    public function withMap(array $map): self
    {
        $self = clone $this;
        $self->map = $map;

        return $self;
    }
}
