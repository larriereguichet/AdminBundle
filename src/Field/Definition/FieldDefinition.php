<?php

namespace LAG\AdminBundle\Field\Definition;

class FieldDefinition implements FieldDefinitionInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $formOptions;

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
