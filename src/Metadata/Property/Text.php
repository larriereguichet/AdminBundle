<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

class Text extends AbstractProperty
{
    public function __construct(
        string $name,
        ?string $propertyPath = null,
        ?string $label = null,
        ?string $template = '@LAGAdmin/grids/properties/text.html.twig',
        bool $sortable = true,
        bool $translatable = false,
        ?string $translationDomain = null,
        array $attr = [],
        array $headerAttr = [],
        private int $length = 100,
        private string $replace = '...',
        private string $emptyString = '~',
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
        );
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function withLength(int $length): self
    {
        $self = clone $this;
        $self->length = $length;

        return $self;
    }

    public function getReplace(): string
    {
        return $this->replace;
    }

    public function withReplace(string $replace): self
    {
        $self = clone $this;
        $self->replace = $replace;

        return $self;
    }

    public function getEmptyString(): string
    {
        return $this->emptyString;
    }

    public function setEmptyString(string $emptyString): self
    {
        $self = clone $this;
        $self->emptyString = $emptyString;

        return $self;
    }
}
