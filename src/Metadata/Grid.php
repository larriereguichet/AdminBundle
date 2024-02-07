<?php

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Form\Type\Resource\ResourceType;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Grid
{
    public function __construct(
        #[Assert\NotBlank(message: 'The grid name should not be empty')]
        private ?string $name = null,

        #[Assert\NotBlank(message: 'The grid type should not be empty')]
        private ?string $type = null,

        #[Assert\NotBlank(message: 'The grid template should not be empty')]
        private ?string $template = null,

        private ?string $translationDomain = null,

        private array $properties = [],

        private array $attributes = [],
        private array $rowAttributes = [],
        private array $headerRowAttributes = [],
        private array $headerAttributes = [],
        private array $options = [],

        private ?string $form = ResourceType::class,

        private array $formOptions = [],
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function withName(?string $name): Grid
    {
        $self = clone $this;
        $self->name = $name;

        return $self;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function withType(?string $type): Grid
    {
        $self = clone $this;
        $self->type = $type;

        return $self;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function withTemplate(?string $template): Grid
    {
        $self = clone $this;
        $self->template = $template;

        return $self;
    }

    public function getTranslationDomain(): ?string
    {
        return $this->translationDomain;
    }

    public function withTranslationDomain(?string $translationDomain): Grid
    {
        $self = clone $this;
        $self->translationDomain = $translationDomain;

        return $self;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function withProperties(array $properties): Grid
    {
        $this->properties = $properties;
        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function withAttributes(array $attributes): Grid
    {
        $self = clone $this;
        $self->attributes = $attributes;
        return $self;
    }

    public function getRowAttributes(): array
    {
        return $this->rowAttributes;
    }

    public function withRowAttributes(array $rowAttributes): Grid
    {
        $self = clone $this;
        $self->rowAttributes = $rowAttributes;
        return $self;
    }

    public function getHeaderRowAttributes(): array
    {
        return $this->headerRowAttributes;
    }

    public function withHeaderRowAttributes(array $headerRowAttributes): Grid
    {
        $self = clone $this;
        $self->headerRowAttributes = $headerRowAttributes;
        return $self;
    }

    public function getHeaderAttributes(): array
    {
        return $this->headerAttributes;
    }

    public function withHeaderAttributes(array $headerAttributes): Grid
    {
        $self = clone $this;
        $self->headerAttributes = $headerAttributes;

        return $self;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function withOptions(array $options): Grid
    {
        $self = clone $this;
        $self->options = $options;

        return $self;
    }

    public function getForm(): ?string
    {
        return $this->form;
    }

    public function withForm(?string $form): Grid
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
}
