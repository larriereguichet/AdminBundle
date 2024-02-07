<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
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
            sortable: $sortable,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attributes: $attributes,
            headerAttributes: $headerAttributes,
            allowedDataType: $allowedDataType,
            length: $length,
            replace: $replace,
            empty: $empty,
            prefix: $prefix,
            suffix: $suffix,
        );
    }
}
