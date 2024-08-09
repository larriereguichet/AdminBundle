<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

/**
 * Interface for collection operations. It adds the required attributes for collection handling to the item operation
 * interface.
 */
interface CollectionOperationInterface extends OperationInterface
{
    public function usePagination(): bool;

    public function setPagination(bool $pagination): self;

    public function getItemsPerPage(): int;

    public function withItemsPerPage(int $itemsPerPage): self;

    public function getPageParameter(): string;

    public function withPageParameter(string $pageParameter): self;

    public function getCriteria(): array;

    public function withCriteria(array $criteria): self;

    public function getOrderBy(): array;

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

    public function getFilterFormOptions(): array;

    public function withFilterFormOptions(array $filterFormOptions): self;

    public function withGridOptions(array $gridOptions): self;

    public function getGridOptions(): array;

    public function getItemForm(): ?string;

    public function withItemForm(?string $itemForm): self;

    public function getItemFormOptions(): ?array;

    public function withItemFormOptions(?array $itemFormOptions): self;

    public function getCollectionForm(): ?string;

    public function withCollectionForm(?string $collectionForm): self;

    public function getCollectionFormOptions(): ?array;

    public function withCollectionFormOptions(?array $collectionFormOptions): self;

    public function getCollectionActions(): ?array;

    public function withCollectionActions(?array $collectionActions): self;
}
