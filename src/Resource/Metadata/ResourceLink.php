<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use Symfony\Component\Validator\Constraints\NotBlank;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class ResourceLink extends Text
{
    public function __construct(
        ?string $name = null,
        string|bool $propertyPath = true,
        string|bool|null $label = null,
        bool $sortable = true,
        bool $translatable = false,
        ?string $translationDomain = null,
        array $attributes = [],
        array $headerAttributes = [],
        int $length = 100,
        string $replace = '...',
        string $emptyString = '~',
        ?array $permissions = null,
        ?string $condition = null,
        ?string $sortingPath = null,

        #[NotBlank]
        private ?string $application = null,

        #[NotBlank]
        private ?string $resource = null,

        #[NotBlank]
        private ?string $operation = null,
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            sortable: $sortable,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attributes: $attributes,
            headerAttributes: $headerAttributes,
            permissions: $permissions,
            condition: $condition,
            sortingPath: $sortingPath,
            length: $length,
            replace: $replace,
            empty: $emptyString,
        );
    }

    public function getApplication(): ?string
    {
        return $this->application;
    }

    public function withApplication(?string $application): self
    {
        $self = clone $this;
        $self->application = $application;

        return $self;
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
