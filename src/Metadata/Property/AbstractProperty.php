<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

abstract class AbstractProperty implements PropertyInterface
{
    public function __construct(
        private string $name,
        private ?string $propertyPath,
        private ?string $label = null,
        private ?string $template = null,
        private bool $mapped = true,
        private bool $sortable = true,
        private bool $translation = false,
        private ?string $translationDomain = 'admin',
        private array $attr = [],
        private array $headerAttr = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function withName(string $property): self
    {
        $self = clone $this;
        $self->name = $property;

        return $self;
    }

    public function getPropertyPath(): ?string
    {
        return $this->propertyPath;
    }

    public function withPropertyPath(?string $propertyPath): self
    {
        $self = clone $this;
        $self->propertyPath = $propertyPath;

        return $self;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function withLabel(?string $label): self
    {
        $self = clone $this;
        $self->label = $label;

        return $self;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function withTemplate(?string $template): self
    {
        $self = clone $this;
        $self->template = $template;

        return $self;
    }

    public function isMapped(): bool
    {
        return $this->mapped;
    }

    public function withMapped(bool $mapped): self
    {
        $self = clone $this;
        $self->mapped = $mapped;

        return $self;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function withSortable(bool $sortable): self
    {
        $self = clone $this;
        $self->sortable = $sortable;

        return $self;
    }

    public function isTranslation(): bool
    {
        return $this->translation;
    }

    public function withTranslation(bool $translation): self
    {
        $self = clone $this;
        $self->translation = $translation;

        return $self;
    }

    public function getTranslationDomain(): ?string
    {
        return $this->translationDomain;
    }

    public function withTranslationDomain(?string $translationDomain): self
    {
        $self = clone $this;
        $self->translationDomain = $translationDomain;

        return $self;
    }

    public function getAttr(): array
    {
        return $this->attr;
    }

    public function withAttr(array $attr): self
    {
        $self = clone $this;
        $self->attr = $attr;

        return $self;
    }

    public function getHeaderAttr(): array
    {
        return $this->headerAttr;
    }

    public function withHeaderAttr(array $headerAttr): self
    {
        $self = clone $this;
        $self->headerAttr = $headerAttr;

        return $self;
    }
}
