<?php

namespace LAG\AdminBundle\Field\View;

use Closure;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\Field\ViewException;

class TextView implements View
{
    private string $name;
    private array $options;
    private ?Closure $dataTransformer;

    public function __construct(string $name, array $options = [], ?Closure $dataTransformer = null)
    {
        $this->name = $name;
        $this->options = $options;
        $this->dataTransformer = $dataTransformer;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOption(string $name)
    {
        if (!\array_key_exists($name, $this->options)) {
            throw new Exception('Invalid option "'.$name.'" for field "'.$this->getName().'"');
        }

        return $this->options[$name];
    }

    public function getTemplate(): string
    {
        throw new ViewException('The getTemplate() method does not exists for TextView');
    }

    public function getDataTransformer(): ?Closure
    {
        return $this->dataTransformer;
    }
}
