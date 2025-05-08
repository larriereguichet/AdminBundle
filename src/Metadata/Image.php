<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Bridge\LiipImagine\DataTransformer\ImageDataTransformer;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Image extends Property
{
    public function __construct(
        ?string $name = null,
        string|bool|null $propertyPath = null,
        string|bool|null $label = null,
        ?string $template = '@LAGAdmin/grids/properties/image.html.twig',
        bool $sortable = false,
        bool $translatable = true,
        ?string $translationDomain = null,
        array $attributes = [],
        array $rowAttributes = [],
        array $headerAttributes = [],
        ?string $dataTransformer = ImageDataTransformer::class,
        ?array $permissions = null,
        ?string $condition = null,
        ?string $sortingPath = null,

        #[Assert\NotBlank(allowNull: true)]
        private ?string $imageFilter = null,

        #[Assert\NotBlank(allowNull: true)]
        private ?string $storage = null,

        private bool $upload = true,
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: $template,
            sortable: $sortable,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attributes: $attributes,
            rowAttributes: $rowAttributes,
            headerAttributes: $headerAttributes,
            dataTransformer: $dataTransformer,
            permissions: $permissions,
            condition: $condition,
            sortingPath: $sortingPath,
        );
    }

    public function getImageFilter(): ?string
    {
        return $this->imageFilter;
    }

    public function withImageFilter(?string $imageFilter): self
    {
        $self = clone $this;
        $self->imageFilter = $imageFilter;

        return $self;
    }

    public function getStorage(): ?string
    {
        return $this->storage;
    }

    public function withStorage(?string $storage): self
    {
        $self = clone $this;
        $self->storage = $storage;

        return $self;
    }

    public function getUpload(): bool
    {
        return $this->upload;
    }

    public function withUpload(bool $upload): self
    {
        $self = clone $this;
        $self->upload = $upload;

        return $self;
    }
}
