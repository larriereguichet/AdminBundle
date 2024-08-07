<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Collection extends Property
{
    public function __construct(
        ?string $name = null,
        bool|string|null $propertyPath = null,
        string|null|bool $label = null,
        ?string $template = '@LAGAdmin/grids/properties/collection.html.twig',
        ?string $component = null,
        bool $sortable = true,
        bool $translatable = false,
        ?string $translationDomain = null,
        array $attributes = [],
        array $containerAttributes = [],
        array $headerAttributes = [],
        ?string $allowedDataType = null,
        ?string $dataTransformer = null,

        #[Assert\NotNull(message: 'The collection should have an property for each entry')]
        private ?PropertyInterface $entryProperty = null,
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: $template,
            component: $component,
            sortable: $sortable,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attributes: $attributes,
            containerAttributes: $containerAttributes,
            headerAttributes: $headerAttributes,
            allowedDataType: $allowedDataType,
            dataTransformer: $dataTransformer,
        );
    }

    public function getEntryProperty(): ?PropertyInterface
    {
        return $this->entryProperty;
    }

    public function withEntryProperty(PropertyInterface $entryProperty): self
    {
        $clone = clone $this;
        $clone->entryProperty = $entryProperty;

        return $clone;
    }
}
