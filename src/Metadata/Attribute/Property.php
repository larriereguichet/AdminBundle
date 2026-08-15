<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Attribute;

use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Metadata\PropertyMetadataInterface;
use LAG\AdminBundle\Validation\Constraint\TemplateValid;
use LAG\AdminBundle\Validation\Constraint\WorkflowInstalled;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Property implements PropertyInterface, PropertyMetadataInterface
{
    /**
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $rowAttributes
     * @param array<string, mixed> $headerAttributes
     * @param array<string>|null $permissions
     */
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
        private string|bool|null $propertyPath = null,
        #[Assert\NotNull(message: 'The label should not be null. Use false instead to disable the label')]
        private string|bool|null $label = null,
        #[TemplateValid(message: 'The template should be a valid Twig template')]
        #[Assert\NotBlank(message: 'The template should not be blank', allowNull: true)]
        private ?string $template = null,
        private bool $sortable = true,
        private bool $translatable = false,
        private array $attributes = [],
        private array $rowAttributes = [],
        private array $headerAttributes = [],
        #[Assert\NotBlank(message: 'The data transformer should not be empty. Use null instead', allowNull: true)]
        private ?string $dataTransformer = null,
        private ?array $permissions = null,
        #[WorkflowInstalled]
        private ?string $condition = null,
        #[Assert\NotBlank(message: 'The sorting path should not be empty. Use null instead', allowNull: true)]
        #[Assert\Expression(
            expression: '(this.isSortable() and this.getSortingPath() !== null) or !this.isSortable()',
            message: 'The sorting path should not be null if the property is sortable',
        )]
        private ?string $sortingPath = null,
        #[Assert\NotBlank(message: 'The component should not be blank. Use null instead', allowNull: true)]
        private ?string $component = null,
        private ?string $translationDomain = null
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function withName(string $property): static
    {
        $self = clone $this;
        $self->name = $property;

        return $self;
    }

    public function getPropertyPath(): string|bool|null
    {
        return $this->propertyPath;
    }

    public function withPropertyPath(string|bool|null $propertyPath): static
    {
        $self = clone $this;
        $self->propertyPath = $propertyPath;

        return $self;
    }

    public function getLabel(): string|bool|null
    {
        return $this->label;
    }

    public function withLabel(string|bool $label): static
    {
        $self = clone $this;
        $self->label = $label;

        return $self;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function withTemplate(?string $template): static
    {
        $self = clone $this;
        $self->template = $template;

        return $self;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function withSortable(bool $sortable): static
    {
        $self = clone $this;
        $self->sortable = $sortable;

        return $self;
    }

    public function isTranslatable(): bool
    {
        return $this->translatable;
    }

    public function withTranslatable(bool $translatable): static
    {
        $self = clone $this;
        $self->translatable = $translatable;

        return $self;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function withAttributes(array $attributes): static
    {
        $self = clone $this;
        $self->attributes = $attributes;

        return $self;
    }

    public function getAttribute(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    public function withAttribute(string $name, mixed $value): static
    {
        $self = clone $this;
        $self->attributes[$name] = $value;

        return $self;
    }

    public function getRowAttributes(): array
    {
        return $this->rowAttributes;
    }

    public function withRowAttributes(array $attributes): static
    {
        $self = clone $this;
        $self->rowAttributes = $attributes;

        return $self;
    }

    public function getHeaderAttributes(): array
    {
        return $this->headerAttributes;
    }

    public function withHeaderAttributes(array $attributes): static
    {
        $self = clone $this;
        $self->headerAttributes = $attributes;

        return $self;
    }

    public function getDataTransformer(): ?string
    {
        return $this->dataTransformer;
    }

    public function withDataTransformer(?string $dataTransformer): static
    {
        $self = clone $this;
        $self->dataTransformer = $dataTransformer;

        return $self;
    }

    public function getRoles(): ?array
    {
        return $this->permissions;
    }

    public function withPermissions(array $permissions): static
    {
        $self = clone $this;
        $self->permissions = $permissions;

        return $self;
    }

    public function getCondition(): ?string
    {
        return $this->condition;
    }

    public function withCondition(?string $condition): static
    {
        $self = clone $this;
        $self->condition = $condition;

        return $self;
    }

    public function getSortingPath(): ?string
    {
        return $this->sortingPath;
    }

    public function withSortingPath(?string $sortingPath): static
    {
        $self = clone $this;
        $self->sortingPath = $sortingPath;

        return $self;
    }

    public function getComponent(): ?string
    {
        return $this->component;
    }

    public function withComponent(?string $component): static
    {
        $self = clone $this;
        $self->component = $component;

        return $self;
    }

    public function getTranslationDomain(): ?string
    {
        return $this->translationDomain;
    }

    public function withTranslationDomain(?string $translationDomain): static
    {
        $self = clone $this;
        $self->translationDomain = $translationDomain;

        return $self;
    }
}
