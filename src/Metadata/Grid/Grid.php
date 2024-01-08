<?php

namespace LAG\AdminBundle\Metadata\Grid;

use LAG\AdminBundle\Metadata\Property\PropertyInterface;
use Symfony\Component\Validator\Constraints as Assert;

/** @deprecated  */
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
        private array $attributes = [],
        private array $rowAttributes = [],
        private array $headerRowAttributes = [],
        private array $headerAttributes = [],
        private array $fields = [],
        private array $options = [],
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function withName(string $name): GridInterface
    {
        $self = clone $this;
        $self->name = $name;

        return $self;
    }

    public function getTranslationDomain(): ?string
    {
        return $this->translationDomain;
    }

    public function setTranslationDomain(?string $translationDomain): Grid
    {
        $this->translationDomain = $translationDomain;
        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function withAttributes(array $attributes): Grid
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function getRowAttributes(): array
    {
        return $this->rowAttributes;
    }

    public function withRowAttributes(array $rowAttributes): Grid
    {
        $this->rowAttributes = $rowAttributes;
        return $this;
    }

    public function getHeaderRowAttributes(): array
    {
        return $this->headerRowAttributes;
    }

    public function withHeaderRowAttributes(array $headerRowAttributes): Grid
    {
        $this->headerRowAttributes = $headerRowAttributes;
        return $this;
    }

    public function getHeaderAttributes(): array
    {
        return $this->headerAttributes;
    }

    public function withHeaderAttributes(array $headerAttributes): Grid
    {
        $this->headerAttributes = $headerAttributes;
        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): Grid
    {
        $this->fields = $fields;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function withOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }
}
