<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

interface GridMetadataInterface extends GridInterface
{
    public function withName(?string $name): self;

    public function withTitle(string|false|null $title): self;

    public function withType(?string $type): self;

    public function withTemplate(?string $template): self;

    /** @param array<string> $properties */
    public function withProperties(array $properties): self;

    /** @param array<string> $attributes */
    public function withAttributes(array $attributes): self;

    /** @param array<string, mixed> $rowAttributes */
    public function withRowAttributes(array $rowAttributes): self;

    /** @param array<string, mixed> $headerRowAttributes */
    public function withHeaderRowAttributes(array $headerRowAttributes): self;

    /** @param array<string, mixed> $headerAttributes */
    public function withHeaderAttributes(array $headerAttributes): self;

    /** @param array<string, mixed> $options */
    public function withOptions(array $options): self;

    public function withForm(?string $form): self;

    /** @param array<string, mixed> $formOptions */
    public function withFormOptions(array $formOptions): self;

    public function withEmptyMessage(?string $emptyMessage): self;

    public function withSortable(bool $sortable): self;

    /** @param array<string, string> $titleAttributes */
    public function withTitleAttributes(array $titleAttributes): self;

    public function setSortParameter(?string $sortParameter): self;

    public function setOrderParameter(?string $orderParameter): self;

    public function setUseHeaders(?bool $useHeaders): void;
}
