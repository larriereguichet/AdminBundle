<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use LAG\AdminBundle\Bridge\LiipImagine\DataTransformer\ImageDataTransformer;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Image extends Property
{
    public function __construct(
        ?string $name = null,
        ?string $propertyPath = null,
        ?string $label = null,
        bool $translatable = false,
        ?string $translationDomain = null,
        array $attributes = [],
        array $headerAttributes = [],
        ?string $dataTransformer = ImageDataTransformer::class,

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
            template: '@LAGAdmin/grids/properties/image.html.twig',
            sortable: false,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attributes: $attributes,
            headerAttributes: $headerAttributes,
            allowedDataType: 'string',
            dataTransformer: $dataTransformer,
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
