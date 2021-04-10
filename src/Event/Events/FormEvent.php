<?php

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Exception\Form\FormMissingException;
use Symfony\Component\Form\FormInterface;

class FormEvent extends AbstractEvent
{
    private array $forms = [];

    public function addForm(string $name, FormInterface $form): self
    {
        $this->forms[$name] = $form;

        return $this;
    }

    public function hasForm(string $name): bool
    {
        return \array_key_exists($name, $this->forms);
    }

    public function removeForm(string $name): self
    {
        if (!$this->hasForm($name)) {
            throw new FormMissingException('The form "'.$name.'" does not exists');
        }
        unset($this->forms[$name]);

        return $this;
    }

    public function getForms(): array
    {
        return $this->forms;
    }
}
