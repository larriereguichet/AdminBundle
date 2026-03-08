<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Metadata\Attribute\Link;

/**
 * Interface for collection operations. It adds the required attributes for collection handling to the item operation
 * interface.
 */
interface CollectionOperationMetadataInterface extends CollectionOperationInterface, OperationMetadataInterface
{
    public function setPagination(bool $pagination): self;

    public function withItemsPerPage(int $itemsPerPage): self;

    public function withPageParameter(string $pageParameter): self;

    /** @param array<string, mixed> $criteria */
    public function withCriteria(array $criteria): self;

    /** @param array<string, mixed> $orderBy */
    public function withOrderBy(array $orderBy): self;

    /** @param array<int, FilterInterface> $filters */
    public function withFilters(array $filters): static;

    public function withFilter(FilterInterface $filter): self;

    public function withGrid(string $grid): self;

    public function withFilterForm(?string $filterForm): self;

    /** @param array<string, mixed> $filterFormOptions */
    public function withFilterFormOptions(array $filterFormOptions): self;

    /** @param array<string, mixed> $gridOptions */
    public function withGridOptions(array $gridOptions): self;

    public function withCollectionForm(?string $collectionForm): self;

    /** @param array<string, mixed> $collectionFormOptions */
    public function withCollectionFormOptions(?array $collectionFormOptions): self;

    /** @param array<int|string, Link> $collectionActions */
    public function withCollectionActions(?array $collectionActions): self;
}
