<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

class Collection extends AbstractProperty
{
    public function __construct(
        string $name,
        string $propertyPath = null,
        ?string $propertyType = Text::class,
        string $label = null,
        ?string $template = '@LAGAdmin/grids/properties/collection.html.twig',
        bool $sortable = true,
        bool $translatable = false,
        string $translationDomain = null,
        array $attr = [],
        array $headerAttr = [],
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
}
