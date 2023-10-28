<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

class Mapped extends AbstractProperty
{
    public function __construct(
        string $name,
        string $propertyPath = null,
        string $label = null,
        ?string $template = '@LAGAdmin/grids/properties/mapped.html.twig',
        bool $sortable = true,
        bool $translatable = true,
        string $translationDomain = null,
        array $attr = [],
        array $headerAttr = [],
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
            attr: $attr,
            headerAttr: $headerAttr,
        );
    }

    public function getMap(): array
    {
        return $this->map;
    }
}
