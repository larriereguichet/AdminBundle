<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use LAG\AdminBundle\Form\Type\Resource\ResourceHiddenType;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Grid
{
    public function __construct(
        #[Assert\NotBlank(message: 'The grid name should not be empty')]
        private ?string $name = null,

        #[Assert\NotBlank(allowNull: true, message: 'The grid title should not be empty. Use null instead')]
        private ?string $title = null,

        #[Assert\NotBlank(message: 'The grid type should not be empty')]
        private ?string $type = null,

        #[Assert\NotBlank(message: 'The grid template should not be an empty string', allowNull: true)]
        private ?string $template = null,

        #[Assert\NotBlank(message: 'The grid component should not be an empty string', allowNull: true)]
        private ?string $component = null,

        private ?string $translationDomain = null,

        #[Assert\GreaterThan(value: 0, message: 'The grid should have at least one property')]
        private array $properties = [],

        private array $attributes = [],
        private array $rowAttributes = [],
        private ?string $headerTemplate = null,
        private array $headerRowAttributes = [],
        private array $headerAttributes = [],
        private array $options = [],

        private ?string $form = ResourceHiddenType::class,

        private array $formOptions = [],

        /** @var array<int, Action> $actions */
        #[Assert\All(constraints: [new Assert\Type(type: Action::class)])]
        #[Assert\Valid]
        #[Assert\NotNull]
        private ?array $actions = null,

        /** @var array<int, Action> $collectionActions */
        #[Assert\All(constraints: [new Assert\Type(type: Action::class)])]
        #[Assert\Valid]
        #[Assert\NotNull]
        private ?array $collectionActions = null,
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function withTitle(?string $title): self
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

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function withProperties(array $properties): self
    {
        $self = clone $this;
        $self->properties = $properties;

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

    public function getRowAttributes(): array
    {
        return $this->rowAttributes;
    }

    public function withRowAttributes(array $rowAttributes): self
    {
        $self = clone $this;
        $self->rowAttributes = $rowAttributes;

        return $self;
    }

    public function getHeaderTemplate(): ?string
    {
        return $this->headerTemplate;
    }

    public function withHeaderTemplate(?string $template): self
    {
        $self = clone $this;
        $self->headerTemplate = $template;

        return $self;
    }

    public function getHeaderRowAttributes(): array
    {
        return $this->headerRowAttributes;
    }

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

    public function withFormOptions(array $formOptions): self
    {
        $self = clone $this;
        $self->formOptions = $formOptions;

        return $self;
    }

    public function getActions(): ?array
    {
        return $this->actions;
    }

    public function withActions(?array $actions): self
    {
        $self = clone $this;
        $self->actions = $actions;

        return $self;
    }

    public function getCollectionActions(): ?array
    {
        return $this->collectionActions;
    }

    public function withCollectionActions(?array $collectionActions): self
    {
        $self = clone $this;
        $self->collectionActions = $collectionActions;

        return $self;
    }
}
