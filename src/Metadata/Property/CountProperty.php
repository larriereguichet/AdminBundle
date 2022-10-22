<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

class CountProperty extends StringProperty
{
    public function __construct(
        string $name,
        ?string $propertyPath = null,
        ?string $label = null,
        ?string $template = '@LAGAdmin/fields/count.html.twig',
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
