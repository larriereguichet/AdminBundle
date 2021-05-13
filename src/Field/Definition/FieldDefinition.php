<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Field\Definition;

class FieldDefinition implements FieldDefinitionInterface
{
    private string $type;
    private array $options;
    private array $formOptions;

    public function __construct(string $type, array $options = [], array $formOptions = [])
    {
        $this->type = $type;
        $this->options = $options;
        $this->formOptions = $formOptions;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getFormOptions(): array
    {
        return $this->formOptions;
    }
}
