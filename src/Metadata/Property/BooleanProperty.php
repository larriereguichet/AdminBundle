<?php

namespace LAG\AdminBundle\Metadata\Property;

class BooleanProperty extends AbstractProperty
{
    public function __construct(
        string $name,
        ?string $propertyPath = null,
        ?string $label = null,
        ?string $template = '@LAGAdmin/fields/boolean.html.twig',
        bool $mapped = true,
        bool $sortable = true,
        bool $translation = false,
        ?string $translationDomain = 'admin',
        array $attr = [],
        array $headerAttr = [],
    ) {
        parent::__construct(
            $name,
            $propertyPath,
            $label,
            $template,
            $mapped,
            $sortable,
            $translation,
            $translationDomain,
            $attr,
            $headerAttr,
        );
    }
}
