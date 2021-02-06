<?php

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Field\Definition\FieldDefinitionInterface;
use Symfony\Contracts\EventDispatcher\Event;

class FieldDefinitionEvent extends Event
{
    private string $class;
    private array $definitions = [];

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function addDefinition(string $name, FieldDefinitionInterface $definition): void
    {
        $this->definitions[$name] = $definition;
    }

    public function removeDefinition(string $name): void
    {
        if ($this->hasDefinition($name)) {
            throw new Exception('The definition for the field "'.$name.'" does not exists');
        }
        unset($this->definitions[$name]);
    }

    public function hasDefinition(string $name): bool
    {
        return key_exists($name, $this->definitions);
    }

    /**
     * @return FieldDefinitionInterface[]
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }
}
