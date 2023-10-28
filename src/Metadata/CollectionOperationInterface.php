<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Metadata\Filter\FilterInterface;

/**
 * Interface for collection operations. It adds the required attributes for collection handling to the item operation
 * interface.
 */
interface CollectionOperationInterface extends OperationInterface
{
    public function hasPagination(): bool;

    public function setPagination(bool $pagination): self;

    public function getItemPerPage(): int;

    public function withItemPerPage(int $itemPerPage): self;

    public function getPageParameter(): string;

    public function withPageParameter(string $pageParameter): self;

    public function getCriteria(): array;

    public function withCriteria(array $criteria): self;

    public function getOrderBy(): array;

    public function withOrderBy(array $orderBy): self;

    /** @return array<int, FilterInterface>|null */
    public function getFilters(): ?array;

    public function getFilter(string $name): ?FilterInterface;

    public function hasFilters(): bool;

    /** @param array<int, FilterInterface> $filters */
    public function withFilters(array $filters): self;

    public function getGrid(): ?string;

    public function withGridTemplate(?string $gridTemplate): self;

    public function getFilterFormType(): ?string;

    public function withFilterFormType(?string $filterForm): self;

    public function getFilterFormOptions(): array;

    public function withFilterFormOptions(array $filterFormOptions): self;
}
