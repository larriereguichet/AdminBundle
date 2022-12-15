<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Field\View;

use LAG\AdminBundle\Exception\Exception;

class FieldView implements View
{
    private string $fieldName;
    private string $template;
    private array $options;
    private ?\Closure $dataTransformer;

    public function __construct(
        string $fieldName,
        string $template,
        array $options = [],
        ?\Closure $dataTransformer = null
    ) {
        $this->fieldName = $fieldName;
        $this->template = $template;
        $this->options = $options;
        $this->dataTransformer = $dataTransformer;
    }

    public function getName(): string
    {
        return $this->fieldName;
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
        return $this->template;
    }

    public function getDataTransformer(): ?\Closure
    {
        return $this->dataTransformer;
    }
}
