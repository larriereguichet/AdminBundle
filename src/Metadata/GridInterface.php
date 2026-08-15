<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

interface GridInterface
{
    public function getName(): ?string;

    public function getTitle(): string|false|null;

    public function getType(): ?string;

    public function getTemplate(): ?string;

    /** @return array<string, string> */
    public function getProperties(): array;

    public function hasProperties(): bool;

    /** @return array<string, string> */
    public function getAttributes(): array;

    /** @return array<string, string> */
    public function getRowAttributes(): array;

    /** @return array<string, string> */
    public function getHeaderRowAttributes(): array;

    /** @return array<string, string> */
    public function getHeaderAttributes(): array;

    /** @return array<string, mixed> */
    public function getOptions(): array;

    public function getForm(): ?string;

    /** @return array<string, mixed> */
    public function getFormOptions(): array;

    public function getEmptyMessage(): ?string;

    public function isSortable(): ?bool;

    /** @return array<string, string> */
    public function getTitleAttributes(): array;

    public function getSortParameter(): string;

    public function getOrderParameter(): string;

    public function setOrderParameter(?string $orderParameter): self;

    public function useHeaders(): ?bool;

    public function setUseHeaders(?bool $useHeaders): void;

    public function getComponent(): ?string;
}
