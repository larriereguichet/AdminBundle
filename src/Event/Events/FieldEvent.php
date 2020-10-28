<?php

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Field\FieldInterface;
use Symfony\Contracts\EventDispatcher\Event;

class FieldEvent extends Event
{
    private string $fieldName;
    private ?string $type;
    private ?FieldInterface $field;
    private array $options;
    private array $context;

    public function __construct(
        string $fieldName,
        string $type = null,
        array $options = [],
        array $context = [],
        FieldInterface $field = null
    ) {
        $this->fieldName = $fieldName;
        $this->type = $type;
        $this->field = $field;
        $this->options = $options;
        $this->context = $context;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getField(): ?FieldInterface
    {
        return $this->field;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
