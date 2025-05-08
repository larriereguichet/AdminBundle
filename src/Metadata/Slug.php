<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Slug extends Property
{
    public function __construct(
        ?string $name = null,
        string|bool|null $propertyPath = null,
        string|bool|null $label = null,
        ?string $template = '@LAGAdmin/grids/properties/slug.html.twig',
        bool $sortable = false,
        bool $translatable = true,
        ?string $translationDomain = null,
        array $attributes = [],
        array $rowAttributes = [],
        array $headerAttributes = [],
        ?string $dataTransformer = null,
        ?array $permissions = null,
        ?string $condition = null,
        ?string $sortingPath = null,

        #[Assert\NotBlank(message: 'The source property should not be blank')]
        private string $source = 'name',

        #[Assert\NotBlank(message: 'The slugger should not be blank')]
        private string $slugger = 'default',
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label, template: $template,
            sortable: $sortable,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attributes: $attributes,
            rowAttributes: $rowAttributes,
            headerAttributes: $headerAttributes,
            dataTransformer: $dataTransformer,
            permissions: $permissions,
            condition: $condition,
            sortingPath: $sortingPath
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
