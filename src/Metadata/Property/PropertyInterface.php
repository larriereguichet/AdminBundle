<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

interface PropertyInterface
{
    public function getName(): string;

    public function withName(string $property): self;

    public function getPropertyPath(): ?string;

    public function withPropertyPath(?string $propertyPath): self;

    public function getLabel(): ?string;

    public function withLabel(?string $label): self;

    public function getTemplate(): ?string;

    public function withTemplate(?string $template): self;

    public function isSortable(): bool;

    public function withSortable(bool $sortable): self;

    public function isTranslatable(): bool;

    public function withTranslatable(bool $translatable): self;

    public function getAttr(): array;

    public function withAttr(array $attr): self;

    public function getHeaderAttr(): array;

    public function withHeaderAttr(array $headerAttr): self;

    public function getTranslationDomain(): ?string;

    public function withTranslationDomain(?string $translationDomain): self;
}
