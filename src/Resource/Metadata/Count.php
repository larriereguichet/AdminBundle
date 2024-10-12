<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use LAG\AdminBundle\Grid\DataTransformer\CountDataTransformer;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Count extends Property
{
    public function __construct(
        ?string $name = null,
        ?string $propertyPath = null,
        ?string $label = null,
        bool $sortable = true,
        array $attributes = [],
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
            template: '@LAGAdmin/grids/properties/count.html.twig',
            sortable: $sortable,
            attributes: $attributes,
            headerAttributes: $headerAttributes,
            dataTransformer: $dataTransformer,
            permissions: $permissions,
            condition: $condition,
            sortingPath: $sortingPath,
        );
    }
}
