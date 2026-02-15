<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Metadata\Attribute\Action;

interface GridInterface
{
    public function getName(): ?string;

    public function getTitle(): ?string;

    public function getType(): ?string;

    public function getTemplate(): ?string;

    public function getTranslationDomain(): ?string;

    /** @return array<string, string> */
    public function getProperties(): array;

    public function hasProperties(): bool;

    /** @return array<string, string> */
    public function getAttributes(): array;

    /** @return array<string, string> */
    public function getRowAttributes(): array;

    /**
     * @return array<string, string>
     */
    public function getHeaderRowAttributes(): array;

    /** @return array<string, string> */
    public function getHeaderAttributes(): array;

    /** @return array<string, mixed> */
    public function getOptions(): array;

    public function getForm(): ?string;

    /** @return array<string, mixed> */
    public function getFormOptions(): array;

    /** @return array<string, Action>|null */
    public function getActions(): ?array;

    /** @return array<string, Action>|null */
    public function getCollectionActions(): ?array;

    public function getEmptyMessage(): ?string;

    public function isSortable(): ?bool;

    /** @return array<string, string> */
    public function getTitleAttributes(): array;

    public function getSortParameter(): string;

    public function getOrderParameter(): string;

    public function setOrderParameter(?string $orderParameter): self;

    public function useHeaders(): ?bool;

    public function setUseHeaders(?bool $useHeaders): void;
}
