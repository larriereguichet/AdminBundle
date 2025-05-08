<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class Filter implements FilterInterface
{
    public function __construct(
        #[Assert\NotBlank]
        private string $name,

        #[Assert\NotBlank]
        private string $comparator = '=',

        #[Assert\NotBlank]
        private string $operator = 'and',

        #[Assert\NotBlank]
        private string $formType = TextType::class,

        private array $formOptions = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function withName(string $name): self
    {
        $self = clone $this;
        $self->name = $name;

        return $self;
    }

    public function getComparator(): string
    {
        return $this->comparator;
    }

    public function withComparator(string $comparator): self
    {
        $self = clone $this;
        $self->comparator = $comparator;

        return $self;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function withOperator(string $operator): self
    {
        $self = clone $this;
        $self->operator = $operator;

        return $self;
    }

    public function getFormType(): string
    {
        return $this->formType;
    }

    public function withFormType(string $formType): FilterInterface
    {
        $self = clone $this;
        $self->formType = $formType;

        return $self;
    }

    public function getFormOptions(): array
    {
        return $this->formOptions;
    }

    public function withFormOptions(array $formOptions): FilterInterface
    {
        $self = clone $this;
        $self->formOptions = $formOptions;

        return $self;
    }
}
