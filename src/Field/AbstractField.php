<?php

namespace LAG\AdminBundle\Field;

use Closure;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Field\View\FieldView;
use LAG\AdminBundle\Field\View\View;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractField implements FieldInterface
{
    private string $name;
    private string $type;
    private array $options = [];
    private bool $frozen = false;

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    public function setOptions(array $options): void
    {
        if ($this->frozen) {
            throw new Exception('The options for the field "'.$this->name.'" have already been configured');
        }
        $this->options = $options;
        $this->frozen = true;
    }

    public function createView(): View
    {
        return new FieldView(
            $this->name,
            $this->getOption('template'),
            $this->getOptions(),
            $this->getDataTransformer()
        );
    }

    public function getParent(): ?string
    {
        return null;
    }

    public function getDataTransformer(): ?Closure
    {
        return null;
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
            throw new Exception('Invalid option "'.$name.'" for field "'.$this->name.'"');
        }

        return $this->options[$name];
    }

    public function getType(): string
    {
        return $this->type;
    }
}
