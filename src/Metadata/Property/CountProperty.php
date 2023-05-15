<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

use LAG\AdminBundle\Grid\DataTransformer\CallbackTransformer;

class CountProperty extends StringProperty
{
    public function __construct(
        string $name,
        ?string $propertyPath = null,
        ?string $label = null,
        ?string $template = '@LAGAdmin/grid/properties/count.html.twig',
        bool $mapped = true,
        bool $sortable = true,
        bool $translation = false,
        ?string $translationDomain = null,
        array $attr = [],
        array $headerAttr = [],
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: $template,
            mapped: $mapped,
            sortable: $sortable,
            translation: $translation,
            translationDomain: $translationDomain,
            attr: $attr,
            headerAttr: $headerAttr,
            dataTransformer: new CallbackTransformer(function ($data) {
                return \count($data);
            }),
        );
    }
}
