<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Attribute;

use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\GridMetadataInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Grid implements GridInterface, GridMetadataInterface
{
    /**
     * @param array<string> $properties
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $rowAttributes
     * @param array<string, mixed> $headerRowAttributes
     * @param array<string, mixed> $headerAttributes
     * @param array<string, mixed> $titleAttributes
     * @param array<string, mixed> $options
     * @param array<string, mixed> $formOptions
     */
    public function __construct(
        #[Assert\NotBlank(message: 'The grid name should not be empty')]
        private ?string $name = null,

        #[Assert\Length(max: 255, maxMessage: 'The grid title should be shorter than 255 characters')]
        private string|false|null $title = null,

        #[Assert\NotBlank(message: 'The grid type should not be empty')]
        private ?string $type = null,

        #[Assert\NotBlank(message: 'The grid template should not be an empty string', allowNull: true)]
        private ?string $template = null,

        #[Assert\NotBlank(message: 'The grid component should not be an empty string', allowNull: true)]
        private ?string $component = null,

        private array $properties = [],

        private array $attributes = [],
        private array $rowAttributes = [],
        private array $headerRowAttributes = [],
        private array $headerAttributes = [],
        private array $titleAttributes = [],
        private array $options = [],

        private ?string $form = FormType::class,

        private array $formOptions = [],

        private ?string $emptyMessage = null,

        #[Assert\NotNull]
        private ?bool $useHeaders = true,

        private bool $sortable = false,

        #[Assert\NotBlank]
        private string $sortParameter = 'sort',

        #[Assert\NotBlank]
        private string $orderParameter = 'order',
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function withName(?string $name): self
    {
        $self = clone $this;
        $self->name = $name;

        return $self;
    }

    public function getTitle(): string|false|null
    {
        return $this->title;
    }

    public function withTitle(string|false|null $title): self
    {
        $self = clone $this;
        $self->title = $title;

        return $self;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function withType(?string $type): self
    {
        $self = clone $this;
        $self->type = $type;

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

    public function getProperties(): array
    {
        return $this->properties;
    }

    /** @param array<string, mixed> $properties */
    public function withProperties(array $properties): self
    {
        $self = clone $this;
        $self->properties = $properties;

        return $self;
    }

    public function hasProperties(): bool
    {
        return \count($this->properties) > 0;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /** @param array<string, mixed> $attributes */
    public function withAttributes(array $attributes): self
    {
        $self = clone $this;
        $self->attributes = $attributes;

        return $self;
    }

    public function getRowAttributes(): array
    {
        return $this->rowAttributes;
    }

    /** @param array<string, mixed> $rowAttributes */
    public function withRowAttributes(array $rowAttributes): self
    {
        $self = clone $this;
        $self->rowAttributes = $rowAttributes;

        return $self;
    }

    public function getHeaderRowAttributes(): array
    {
        return $this->headerRowAttributes;
    }

    /** @param array<string, mixed> $headerRowAttributes */
    public function withHeaderRowAttributes(array $headerRowAttributes): self
    {
        $self = clone $this;
        $self->headerRowAttributes = $headerRowAttributes;

        return $self;
    }

    public function getHeaderAttributes(): array
    {
        return $this->headerAttributes;
    }

    /** @param array<string, mixed> $headerAttributes */
    public function withHeaderAttributes(array $headerAttributes): self
    {
        $self = clone $this;
        $self->headerAttributes = $headerAttributes;

        return $self;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    /** @param array<string, mixed> $options */
    public function withOptions(array $options): self
    {
        $self = clone $this;
        $self->options = $options;

        return $self;
    }

    public function getForm(): ?string
    {
        return $this->form;
    }

    public function withForm(?string $form): self
    {
        $self = clone $this;
        $self->form = $form;

        return $self;
    }

    public function getFormOptions(): array
    {
        return $this->formOptions;
    }

    /** @param array<string, mixed> $formOptions */
    public function withFormOptions(array $formOptions): self
    {
        $self = clone $this;
        $self->formOptions = $formOptions;

        return $self;
    }

    public function getEmptyMessage(): ?string
    {
        return $this->emptyMessage;
    }

    public function withEmptyMessage(?string $emptyMessage): self
    {
        $self = clone $this;
        $self->emptyMessage = $emptyMessage;

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

    /** @return array<string, string> */
    public function getTitleAttributes(): array
    {
        return $this->titleAttributes;
    }

    /** @param array<string, string> $titleAttributes */
    public function withTitleAttributes(array $titleAttributes): self
    {
        $self = clone $this;
        $self->titleAttributes = $titleAttributes;

        return $self;
    }

    public function getSortParameter(): string
    {
        return $this->sortParameter;
    }

    public function setSortParameter(?string $sortParameter): self
    {
        $this->sortParameter = $sortParameter;

        return $this;
    }

    public function getOrderParameter(): string
    {
        return $this->orderParameter;
    }

    public function setOrderParameter(?string $orderParameter): self
    {
        $this->orderParameter = $orderParameter;

        return $this;
    }

    public function useHeaders(): ?bool
    {
        return $this->useHeaders;
    }

    public function setUseHeaders(?bool $useHeaders): void
    {
        $this->useHeaders = $useHeaders;
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
}
