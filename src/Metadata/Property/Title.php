<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

#[\Attribute]
class Title extends Text
{
    public function __construct(
        ?string $name = null,
        ?string $propertyPath = null,
        ?string $label = null,
        bool $sortable = true,
        bool $translatable = false,
        ?string $translationDomain = null,
        array $attributes = [],
        array $headerAttributes = [],
        ?string $allowedDataType = null,
        array $grids = [],
        int $length = 100,
        string $replace = '...',
        string $empty = '~',
        string $suffix = '',
        string $prefix = '',
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: '@LAGAdmin/grids/properties/title.html.twig',
            sortable: $sortable,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attributes: $attributes,
            headerAttributes: $headerAttributes,
            allowedDataType: $allowedDataType,
            grids: $grids,
            length: $length,
            replace: $replace,
            empty: $empty,
            prefix: $prefix,
            suffix: $suffix,
        );
    }
}
