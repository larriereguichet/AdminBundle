<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

class Mapped extends Property
{
    public function __construct(
        ?string $name = null,
        string $propertyPath = null,
        string $label = null,
        ?string $template = '@LAGAdmin/grids/properties/mapped.html.twig',
        bool $sortable = true,
        bool $translatable = true,
        string $translationDomain = null,
        array $attributes = [],
        array $headerAttributes = [],
        private array $map = [],
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

    public function getMap(): array
    {
        return $this->map;
    }
}
