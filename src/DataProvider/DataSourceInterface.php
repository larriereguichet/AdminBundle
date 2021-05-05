<?php

namespace LAG\AdminBundle\DataProvider;

use LAG\AdminBundle\Filter\FilterInterface;

interface DataSourceInterface
{
    /**
     * Return the data associated to the data source provided by the data provider.
     *
     * @return mixed
     */
    public function getData();

    /**
     * Return true if the data source should be paginated.
     *
     * @return bool
     */
    public function isPaginated(): bool;

    /**
     * Return the current page.
     *
     * @return int
     */
    public function getPage(): int;

    /**
     * Return the number of elements displayed in a page.
     *
     * @return int
     */
    public function getMaxPerPage(): int;

    /**
     * Return filters associated to the data source.
     *
     * @return FilterInterface[]
     */
    public function getFilters(): array;

    /**
     * Return orders.
     *
     * @return array
     */
    public function getOrderBy(): array;
}
