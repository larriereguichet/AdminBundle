<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DataProvider;

use LAG\AdminBundle\Filter\FilterInterface;

/** @deprecated  */
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
     */
    public function isPaginated(): bool;

    /**
     * Return the current page.
     */
    public function getPage(): int;

    /**
     * Return the number of elements displayed in a page.
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
     */
    public function getOrderBy(): array;
}
