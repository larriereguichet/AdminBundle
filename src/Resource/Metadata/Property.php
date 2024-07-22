<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use LAG\AdminBundle\Validation\Constraint\TemplateValid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
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

        #[Assert\NotNull(message: 'The property path should not be null. Use false instead')]
        private string|null|bool $propertyPath = null,

        #[Assert\NotNull(message: 'The label should not be null. Use false instead to disable the label')]
        private string|null|bool $label = null,

        #[TemplateValid(message: 'The template should be a valid Twig template')]
        #[NotBlank(message: 'The template should not be blank')]
        private ?string $template = null,

        private ?string $component = null,

        private bool $sortable = true,

        private bool $translatable = false,

        #[Assert\NotBlank(allowNull: true, message: 'The translation domain should not be empty. Use null instead')]
        private ?string $translationDomain = null,

        private array $attributes = [],

        private array $containerAttributes = [],

        private array $headerAttributes = [],

        #[Assert\NotBlank(allowNull: true, message: 'The allowed data types should not be empty. Use null instead')]
        private ?string $allowedDataType = null,

        #[Assert\NotBlank(allowNull: true, message: 'The data transformer should not be empty. Use null instead')]
        private ?string $dataTransformer = null,
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

    public function getPropertyPath(): string|null|bool
    {
        return $this->propertyPath;
    }

    public function withPropertyPath(string|bool $propertyPath): self
    {
        $self = clone $this;
        $self->propertyPath = $propertyPath;

        return $self;
    }

    public function getLabel(): string|null|bool
    {
        return $this->label;
    }

    public function withLabel(string|bool $label): self
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

    public function withAttributes(array $attributes): self
    {
        $self = clone $this;
        $self->attributes = $attributes;

        return $self;
    }

    public function getAttribute(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    public function withAttribute(string $name, mixed $value): self
    {
        $self = clone $this;
        $self->attributes[$name] = $value;

        return $self;
    }

    public function getContainerAttributes(): array
    {
        return $this->containerAttributes;
    }

    public function withContainerAttributes(array $attributes): self
    {
        $self = clone $this;
        $self->containerAttributes = $attributes;

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

    public function getComponent(): ?string
    {
        return $this->component;
    }

    public function withComponent(?string $component): self
    {
        $self = clone $this;
        $self->component = $component;

        return $self;
    }

    public function getDataTransformer(): ?string
    {
        return $this->dataTransformer;
    }

    public function withDataTransformer(?string $dataTransformer): self
    {
        $self = clone $this;
        $self->dataTransformer = $dataTransformer;

        return $self;
    }
}
