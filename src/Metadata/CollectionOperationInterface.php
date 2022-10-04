<?php

namespace LAG\AdminBundle\Metadata;

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

    public function getFilters(): array;

    public function hasFilters(): bool;

    public function withFilters(array $filters): self;

    public function getListActions(): array;

    public function withListActions(array $listActions): self;

    public function getGridTemplate(): ?string;

    public function withGridTemplate(?string $gridTemplate): self;
}
