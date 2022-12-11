<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

class MappedProperty extends AbstractProperty
{
    public function __construct(
        string $name,
        ?string $propertyPath,
        ?string $label = null,
        ?string $template = '@LAGAdmin/grid/properties/mapped.html.twig',
        bool $mapped = true,
        bool $sortable = true,
        bool $translation = false,
        ?string $translationDomain = 'admin',
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
        );
    }

    public function getMap(): array
    {
        return $this->map;
    }

    public function withMap(array $map): self
    {
        $self = clone $this;
        $self->map = $map;

        return $self;
    }
}
