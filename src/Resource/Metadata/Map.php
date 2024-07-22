<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Map extends Property
{
    public function __construct(
        ?string $name = null,
        string $propertyPath = null,
        string $label = null,
        bool $sortable = true,
        bool $translatable = true,
        string $translationDomain = null,
        array $attributes = [],
        array $headerAttributes = [],
        ?string $dataTransformer = null,

        #[Assert\Count(min: 1, minMessage: 'The map should have at least elements')]
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
