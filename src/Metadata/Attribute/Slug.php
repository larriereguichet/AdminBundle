<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Attribute;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Slug extends Property
{
    /** @var string[] */
    #[Assert\NotBlank]
    #[Assert\All([new Assert\NotBlank()])]
    private array $source;

    /**
     * @param string|string[] $source
     */
    public function __construct(
        ?string $name = null,
        string|bool|null $propertyPath = null,
        string|bool|null $label = null,
        ?string $template = '@LAGAdmin/grids/properties/slug.html.twig',
        bool $sortable = false,
        bool $translatable = true,

        array $attributes = [],
        array $rowAttributes = [],
        array $headerAttributes = [],
        ?string $dataTransformer = null,
        ?array $permissions = null,
        ?string $condition = null,
        ?string $sortingPath = null,
        ?string $component = null,
        ?string $translationDomain = null,

        string|array $source = 'name',
        #[Assert\NotBlank(message: 'The slugger should not be blank')]
        private string $slugger = 'default',
    ) {
        $this->source = \is_string($source) ? [$source] : $source;

        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label, template: $template,
            sortable: $sortable,
            translatable: $translatable,

            attributes: $attributes,
            rowAttributes: $rowAttributes,
            headerAttributes: $headerAttributes,
            dataTransformer: $dataTransformer,
            permissions: $permissions,
            condition: $condition,
            sortingPath: $sortingPath,
            component: $component,
            translationDomain: $translationDomain,
        );
    }

    /** @return string[] */
    public function getSource(): array
    {
        return $this->source;
    }

    /** @param string|string[] $source */
    public function withSource(string|array $source): self
    {
        $self = clone $this;
        $self->source = \is_string($source) ? [$source] : $source;

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
