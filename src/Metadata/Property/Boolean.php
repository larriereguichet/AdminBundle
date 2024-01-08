<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Boolean extends Property
{
    public function __construct(
        ?string $name = null,
        string $propertyPath = null,
        string $label = null,
        ?string $template = '@LAGAdmin/grids/properties/boolean.html.twig',
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
}
