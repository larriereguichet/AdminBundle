<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use LAG\AdminBundle\Form\Type\Data\HiddenDataType;
use LAG\AdminBundle\Grid\DataTransformer\FormDataTransformer;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Form extends Property implements PropertyInterface
{
    public function __construct(
        ?string $name = null,
        string|bool|null $propertyPath = null,
        string|bool|null $label = null,
        bool $translatable = false,
        ?string $translationDomain = null,
        array $attributes = [],
        array $headerAttributes = [],
        ?string $dataTransformer = FormDataTransformer::class,
        ?array $permissions = null,
        ?string $condition = null,

        private string $form = HiddenDataType::class,
        private ?string $formTemplate = null,
        private array $formOptions = [],
        private array $properties = [],
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: '@LAGAdmin/grids/properties/form.html.twig',
            sortable: false,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attributes: $attributes,
            headerAttributes: $headerAttributes,
            dataTransformer: $dataTransformer,
            permissions: $permissions,
            condition: $condition,
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

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function withProperties(array $properties): self
    {
        $self = clone $this;
        $self->properties = $properties;

        return $self;
    }

    public function getFormTemplate(): ?string
    {
        return $this->formTemplate;
    }

    public function withFormTemplate(string $formTemplate): self
    {
        $self = clone $this;
        $self->formTemplate = $formTemplate;

        return $self;
    }
}
