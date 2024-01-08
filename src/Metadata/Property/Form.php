<?php

namespace LAG\AdminBundle\Metadata\Property;


class Form extends Property
{
    public function __construct(
        ?string $name = null,
        private string $formType,
        private \LAG\AdminBundle\Metadata\Link $targetUrl,
        private array $formOptions = [],
        ?string $propertyPath = '.',
        ?string $template = '@LAGAdmin/grids/properties/form.html.twig',
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            template: $template,
        );
    }

    public function getFormType(): string
    {
        return $this->formType;
    }

    public function setFormType(string $formType): self
    {
        $self = clone $this;
        $self->formType = $formType;

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

    public function getTargetUrl(): \LAG\AdminBundle\Metadata\Link
    {
        return $this->targetUrl;
    }

    public function setTargetUrl(\LAG\AdminBundle\Metadata\Link $targetUrl): self
    {
        $self = clone $this;
        $self->targetUrl = $targetUrl;

        return $self;
    }
}
