<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Slug extends Property
{
    public function __construct(
        private string $source,
        private string $slugger = 'default',

        ?string $name = null,
        bool|string|null $propertyPath = null,
        string|bool|null $label = null,
        ?string $template = '@LAGAdmin/grids/properties/slug.html.twig',
        bool $sortable = true,
        bool $translatable = false,
        ?string $translationDomain = null,
        array $attributes = [],
        array $containerAttributes = [],
        array $headerAttributes = [],
        ?string $dataTransformer = null,
        ?array $permissions = null,
        ?string $condition = null,
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label, template: $template,
            sortable: $sortable,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attributes: $attributes,
            containerAttributes: $containerAttributes,
            headerAttributes: $headerAttributes,
            dataTransformer: $dataTransformer,
            permissions: $permissions,
            condition: $condition,
        );
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function withSource(string $source): self
    {
        $self = clone $this;
        $self->source = $source;

        return $self;
    }

    public function getSlugger(): string
    {
        return $this->slugger;
    }

    public function withSlugger(string $slugger): self
    {
        $self = clone $this;
        $self->slugger = $slugger;

        return $self;
    }
}
