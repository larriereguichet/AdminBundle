<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Metadata\Filter\FilterInterface;

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

    /** @return FilterInterface[]|null */
    public function getFilters(): ?array;

    public function getFilter(string $name): ?FilterInterface;

    public function hasFilters(): bool;

    public function withFilters(array $filters): self;

    public function getGridTemplate(): ?string;

    public function withGridTemplate(?string $gridTemplate): self;
}
