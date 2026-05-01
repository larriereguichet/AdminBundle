<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Attribute;

use LAG\AdminBundle\Grid\DataTransformer\CountDataTransformer;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Count extends Property
{
    public function __construct(
        ?string $name = null,
        string|bool|null $propertyPath = null,
        string|bool|null $label = null,
        ?string $template = '@LAGAdmin/grids/properties/count.html.twig',
        bool $sortable = true,
        bool $translatable = true,

        array $attributes = [],
        array $rowAttributes = [],
        array $headerAttributes = [],
        ?string $dataTransformer = CountDataTransformer::class,
        ?array $permissions = null,
        ?string $condition = null,
        ?string $sortingPath = null,
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
        );
    }
}
