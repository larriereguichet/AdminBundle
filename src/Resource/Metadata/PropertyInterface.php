<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

interface PropertyInterface
{
    public function getName(): ?string;

    public function withName(string $property): self;

    public function getPropertyPath(): string|null|bool;

    public function withPropertyPath(string|false $propertyPath): self;

    public function getLabel(): string|null|bool;

    public function withLabel(string|bool $label): self;

    public function getTemplate(): ?string;

    public function withTemplate(?string $template): self;

    public function isSortable(): bool;

    public function withSortable(bool $sortable): self;

    public function isTranslatable(): bool;

    public function withTranslatable(bool $translatable): self;

    public function getAttributes(): array;

    public function withAttributes(array $attributes): self;

    public function getAttribute(string $name): mixed;

    public function withAttribute(string $name, mixed $value): self;

    public function getContainerAttributes(): array;

    public function withContainerAttributes(array $attributes): self;

    public function getHeaderAttributes(): array;

    public function withHeaderAttributes(array $headerAttributes): self;

    public function getTranslationDomain(): ?string;

    public function withTranslationDomain(?string $translationDomain): self;

    public function getAllowedDataType(): ?string;

    public function withAllowedDataType(?string $allowedDataType): self;

    public function getComponent(): ?string;

    public function withComponent(?string $component): self;

    public function getDataTransformer(): ?string;

    public function withDataTransformer(?string $dataTransformer): self;
}
