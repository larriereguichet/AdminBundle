<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

class Count extends AbstractProperty implements TransformablePropertyInterface
{
    public function __construct(
        string $name,
        ?string $propertyPath = null,
        ?string $label = null,
        ?string $template = '@LAGAdmin/grids/properties/count.html.twig',
        bool $sortable = true,
        array $attr = [],
        array $headerAttr = [],
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: $template,
            sortable: $sortable,
            attr: $attr,
            headerAttr: $headerAttr,
        );
    }

    public function transform(mixed $data): int
    {
        return count($data);
    }
}
