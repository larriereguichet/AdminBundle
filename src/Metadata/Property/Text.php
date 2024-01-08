<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class Text extends Property
{
    public function __construct(
        ?string $name = null,
        ?string $propertyPath = null,
        ?string $label = null,
        ?string $template = null,
        bool $sortable = true,
        bool $translatable = false,
        ?string $translationDomain = null,
        array $attributes = [],
        array $headerAttributes = [],
        ?string $allowedDataType = null,
        array $grids = [],

        #[Assert\Length(min: 1)]
        private int $length = 100,
        private string $replace = '...',
        private string $empty = '~',
        private string $suffix = '',
        private string $prefix = '',
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: '@LAGAdmin/grids/properties/text.html.twig',
            sortable: $sortable,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attributes: $attributes,
            headerAttributes: $headerAttributes,
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

    public function getEmpty(): string
    {
        return $this->empty;
    }

    public function withEmpty(string $empty): self
    {
        $self = clone $this;
        $self->empty = $empty;

        return $self;
    }

    public function getSuffix(): string
    {
        return $this->suffix;
    }

    public function setSuffix(string $suffix): Text
    {
        $self = clone $this;
        $self->suffix = $suffix;

        return $self;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): Text
    {
        $self = clone $this;
        $self->prefix = $prefix;

        return $self;
    }

}
