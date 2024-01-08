<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

class Count extends Property
{
    public function __construct(
        ?string $name = null,
        string $propertyPath = null,
        string $label = null,
        bool $sortable = true,
        array $attributes = [],
        array $headerAttributes = [],
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: '@LAGAdmin/grids/properties/count.html.twig',
            sortable: $sortable,
            attributes: $attributes,
            headerAttributes: $headerAttributes,
        );
    }
}
