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

    public function __construct(string $type, array $options = [])
    {
        $this->type = $type;
        $this->options = $options;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
