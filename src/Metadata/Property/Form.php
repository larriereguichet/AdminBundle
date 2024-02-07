<?php

namespace LAG\AdminBundle\Metadata\Property;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Form extends Property
{
    public function __construct(
        private string $form,
        private array $formOptions = [],
        ?string $name = null,
        string|bool|null $propertyPath = true,
        ?string $template = '@LAGAdmin/grids/properties/form.html.twig',
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            template: $template,
        );
    }

    public function getForm(): string
    {
        return $this->form;
    }

    public function setForm(string $form): self
    {
        $self = clone $this;
        $self->form = $form;

        return $self;
    }

    public function getFormOptions(): array
    {
        return $this->formOptions;
    }

    public function setFormOptions(array $formOptions): self
    {
        $self = clone $this;
        $self->formOptions = $formOptions;

        return $self;
    }
}
