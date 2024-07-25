<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use LAG\AdminBundle\Validation\Constraint\TemplateValid;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractProperty implements \LAG\AdminBundle\Resource\Metadata\PropertyInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        private string $name,
        #[Assert\NotBlank]
        private ?string $propertyPath,
        private ?string $label = null,
        #[TemplateValid]
        private ?string $template = null,
        private bool $sortable = true,
        private bool $translatable = false,
        private ?string $translationDomain = null,
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

    public function getPropertyPath(): string|null|bool
    {
        return $this->propertyPath;
    }

    public function withPropertyPath(string|null|bool $propertyPath): self
    {
        $self = clone $this;
        $self->propertyPath = $propertyPath;

        return $self;
    }

    public function getLabel(): string|null|bool
    {
        return $this->label;
    }

    public function withLabel(string|null|bool $label): self
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

    public function isTranslatable(): bool
    {
        return $this->translatable;
    }

    public function withTranslatable(bool $translatable): self
    {
        $self = clone $this;
        $self->translatable = $translatable;

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
}
