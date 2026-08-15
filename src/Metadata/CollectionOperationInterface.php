<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Metadata\Attribute\Link;

/**
 * Interface for collection operations. It adds the required attributes for collection handling to the item operation
 * interface.
 */
interface CollectionOperationInterface extends OperationInterface
{
    public function getLimit(): ?int;

    public function hasPagination(): bool;

    public function withPagination(bool $pagination): self;

    public function getItemsPerPage(): int;

    public function getPageParameter(): string;

    /**
     * The default ordering of the collection, as a map of property name to direction. It is overridden, entry by
     * entry, by the "order_by" context key and then by the sort and order query parameters.
     *
     * @return array<string, string>
     */
    public function getOrderBy(): array;

    /** @return array<int, FilterInterface>|null */
    public function getFilters(): ?array;

    public function getFilter(string $name): ?FilterInterface;

    public function hasFilter(string $name): bool;

    public function hasFilters(): bool;

    public function getGrid(): ?string;

    public function getFilterForm(): ?string;

    /** @return array<string, mixed> */
    public function getFilterFormOptions(): array;

    /** @return array<string, mixed> */
    public function getGridOptions(): array;

    /** @return string[] */
    public function getBatchOperations(): array;

    /** @return array<int|string, Link>|null */
    public function getCollectionLinks(): ?array;
}
