<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

use LAG\AdminBundle\Grid\DataTransformer\DataTransformerInterface;

#[\Attribute]
class StringProperty extends AbstractProperty
{
    public function __construct(
        string $name,
        ?string $propertyPath = null,
        ?string $label = null,
        ?string $template = '@LAGAdmin/grid/properties/string.html.twig',
        bool $mapped = true,
        bool $sortable = true,
        bool $translation = false,
        ?string $translationDomain = null,
        array $attr = [],
        array $headerAttr = [],
        ?DataTransformerInterface $dataTransformer = null,
        private int $length = 100,
        private string $replace = '...',
        private string $emptyString = '~',
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
            $dataTransformer,
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
