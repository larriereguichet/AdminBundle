<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\DataSource;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\DataProvider\DataSourceInterface;

class ORMDataSource implements DataSourceInterface
{
    public function __construct(
        private QueryBuilder $data,
        private bool $pagination,
        private int $page = 1,
        private int $maxPerPage = 25,
        private array $orderBy = [],
        private array $filters = []
    ) {
    }

    public function getData(): QueryBuilder
    {
        return $this->data;
    }

    public function isPaginated(): bool
    {
        return $this->pagination;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getMaxPerPage(): int
    {
        return $this->maxPerPage;
    }

    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}
