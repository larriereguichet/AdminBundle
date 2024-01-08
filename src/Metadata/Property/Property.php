<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

use LAG\AdminBundle\Validation\Constraint\TemplateValid;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class Property implements PropertyInterface
{
    public function __construct(
        #[Assert\NotBlank(message: 'The name should not be empty')]
        #[Assert\Length(
            min: 1,
            max: 255,
            minMessage: 'The name should be longer than 1 character',
            maxMessage: 'The name should not be longer than 255 characters'
        )]
        private ?string $name = null,

        #[Assert\NotBlank(message: 'The property path should not be empty')]
        private ?string $propertyPath = null,

        private ?string $label = null,

        #[TemplateValid(message: 'The template should be a valid Twig template')]
        private ?string $template = null,

        private bool $sortable = true,

        private bool $translatable = false,

        #[Assert\NotBlank(allowNull: true, message: 'The translation domain should not be empty. Use null instead')]
        private ?string $translationDomain = null,

        private array $attributes = [],

        private array $headerAttributes = [],

        #[Assert\NotBlank(allowNull: true, message: 'The allowed data types should not be empty. Use null instead')]
        private ?string $allowedDataType = null,

        #[Assert\All(constraints: [new Assert\Type('string')])]
        private array $grids = [],
    ) {
    }

    public function getName(): ?string
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

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function withAttr(array $attr): self
    {
        $self = clone $this;
        $self->attributes = $attr;

        return $self;
    }

    public function getHeaderAttributes(): array
    {
        return $this->headerAttributes;
    }

    public function withHeaderAttributes(array $headerAttributes): self
    {
        $self = clone $this;
        $self->headerAttributes = $headerAttributes;

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

    public function getAllowedDataType(): ?string
    {
        return $this->allowedDataType;
    }

    public function withAllowedDataType(?string $allowedDataType): self
    {
        $self = clone $this;
        $self->allowedDataType = $allowedDataType;

        return $self;
    }

    public function getGrids(): array
    {
        return $this->grids;
    }

    public function withGrids(array $grids): self
    {
        $self = clone $this;
        $self->grids = $grids;

        return $self;
    }
}
