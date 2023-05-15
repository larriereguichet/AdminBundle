<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

use LAG\AdminBundle\Grid\DataTransformer\CallbackTransformer;

class MappedProperty extends AbstractProperty
{
    public function __construct(
        string $name,
        ?string $propertyPath = null,
        ?string $label = null,
        ?string $template = '@LAGAdmin/grid/properties/mapped.html.twig',
        bool $mapped = true,
        bool $sortable = true,
        bool $translation = true,
        ?string $translationDomain = null,
        array $attr = [],
        array $headerAttr = [],
        private array $map = [],
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
            new CallbackTransformer(function ($data) {
                return $this->map[$data] ?? null;
            })
        );
    }

    public function getMap(): array
    {
        return $this->map;
    }
}
