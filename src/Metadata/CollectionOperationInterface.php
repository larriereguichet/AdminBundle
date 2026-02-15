<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Metadata\Attribute\Action;

/**
 * Interface for collection operations. It adds the required attributes for collection handling to the item operation
 * interface.
 */
interface CollectionOperationInterface extends OperationInterface
{
    public function hasPagination(): bool;

    public function setPagination(bool $pagination): self;

    public function getItemsPerPage(): int;

    public function withItemsPerPage(int $itemsPerPage): self;

    public function getPageParameter(): string;

    public function withPageParameter(string $pageParameter): self;

    /** @return array<string, mixed> */
    public function getCriteria(): array;

    /** @param array<string, mixed> $criteria */
    public function withCriteria(array $criteria): self;

    /** @return array<string, string> */
    public function getOrderBy(): array;

    /** @param array<string, mixed> $orderBy */
    public function withOrderBy(array $orderBy): self;

    /** @return array<int, FilterInterface>|null */
    public function getFilters(): ?array;

    public function getFilter(string $name): ?FilterInterface;

    public function hasFilter(string $name): bool;

    public function hasFilters(): bool;

    /** @param array<int, FilterInterface> $filters */
    public function withFilters(array $filters): self;

    public function withFilter(FilterInterface $filter): self;

    public function getGrid(): ?string;

    public function withGrid(string $grid): self;

    public function getFilterForm(): ?string;

    public function withFilterForm(?string $filterForm): self;

    /** @return array<string, mixed> */
    public function getFilterFormOptions(): array;

    /** @param array<string, mixed> $filterFormOptions */
    public function withFilterFormOptions(array $filterFormOptions): self;

    /** @param array<string, mixed> $gridOptions */
    public function withGridOptions(array $gridOptions): self;

    /** @return array<string, mixed> */
    public function getGridOptions(): array;

    public function getCollectionForm(): ?string;

    public function withCollectionForm(?string $collectionForm): self;

    /** @return array<string, mixed>|null */
    public function getCollectionFormOptions(): ?array;

    /** @param array<string, mixed> $collectionFormOptions */
    public function withCollectionFormOptions(?array $collectionFormOptions): self;

    /** @return array<int|string, Action>|null */
    public function getCollectionActions(): ?array;

    /** @param array<int|string, Action> $collectionActions */
    public function withCollectionActions(?array $collectionActions): self;
}
