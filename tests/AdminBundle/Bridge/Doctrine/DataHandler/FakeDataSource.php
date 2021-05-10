<?php

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\DataHandler;

use LAG\AdminBundle\DataProvider\DataSourceInterface;

class FakeDataSource implements DataSourceInterface
{
    public function getData()
    {
        return null;
    }

    public function isPaginated(): bool
    {
        return false;
    }

    public function getPage(): int
    {
        return 0;
    }

    public function getMaxPerPage(): int
    {
        return 0;
    }

    public function getFilters(): array
    {
        return [];
    }

    public function getOrderBy(): array
    {
        return [];
    }
}
