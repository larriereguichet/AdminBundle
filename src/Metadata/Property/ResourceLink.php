<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

use Symfony\Component\Validator\Constraints\NotBlank;

class ResourceLink extends Text
{
    public function __construct(
        string $name,
        string $propertyPath = null,
        string $label = null,
        ?string $template = '@LAGAdmin/grids/properties/resource_link.html.twig',
        bool $sortable = true,
        bool $translatable = false,
        string $translationDomain = null,
        array $attr = [],
        array $headerAttr = [],
        int $length = 100,
        string $replace = '...',
        string $emptyString = '~',
        #[NotBlank]
        private ?string $resource = null,
        #[NotBlank]
        private ?string $operation = null,
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: $template,
            sortable: $sortable,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attr: $attr,
            headerAttr: $headerAttr,
            length: $length,
            replace: $replace,
            emptyString: $emptyString,
        );
    }

    public function getResource(): ?string
    {
        return $this->resource;
    }

    public function withResource(?string $resource): self
    {
        $self = clone $this;
        $self->resource = $resource;

        return $self;
    }

    public function getOperation(): ?string
    {
        return $this->operation;
    }

    public function withOperation(?string $operation): self
    {
        $self = clone $this;
        $self->operation = $operation;

        return $self;
    }
}
