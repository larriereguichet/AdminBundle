<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Events\Configuration;

use LAG\AdminBundle\Field\Field;
use Symfony\Contracts\EventDispatcher\Event;

class FieldConfigurationEvent extends Event
{
    private string $fieldName;
    private ?string $type;
    private ?Field $field;
    private array $options;
    private array $context;

    public function __construct(
        string $fieldName,
        string $type = null,
        array $options = [],
        array $context = [],
        Field $field = null
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getField(): ?Field
    {
        return $this->field;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
