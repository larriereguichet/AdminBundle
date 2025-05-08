<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Title extends Property
{
    public function __construct(
        ?string $name = null,
        string|bool|null $propertyPath = null,
        string|bool|null $label = null,
        ?string $template = '@LAGAdmin/grids/properties/title.html.twig',
        bool $sortable = false,
        bool $translatable = true,
        ?string $translationDomain = null,
        array $attributes = [],
        array $rowAttributes = [],
        array $headerAttributes = [],
        ?string $dataTransformer = null,
        ?array $permissions = null,
        ?string $condition = null,
        ?string $sortingPath = null,

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
            template: $template,
            sortable: $sortable,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attributes: $attributes,
            rowAttributes: $rowAttributes,
            headerAttributes: $headerAttributes,
            dataTransformer: $dataTransformer,
            permissions: $permissions,
            condition: $condition,
            sortingPath: $sortingPath,
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

    public function setSuffix(string $suffix): self
    {
        $self = clone $this;
        $self->suffix = $suffix;

        return $self;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): self
    {
        $self = clone $this;
        $self->prefix = $prefix;

        return $self;
    }
}
