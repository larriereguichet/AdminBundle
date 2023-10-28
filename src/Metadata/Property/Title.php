<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

class Title extends Text
{
    public function __construct(
        string $name,
        string $propertyPath = null,
        string $label = null,
        ?string $template = '@LAGAdmin/grids/properties/title.html.twig',
        bool $sortable = true,
        bool $translatable = false,
        string $translationDomain = null,
        array $attr = [],
        array $headerAttr = [],
        int $length = 100,
        string $replace = '...',
        string $emptyString = '~',
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
            length: $length,
            replace: $replace,
            emptyString: $emptyString,
        );
    }
}
