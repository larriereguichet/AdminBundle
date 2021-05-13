<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DataProvider\DataSourceHandler;

use LAG\AdminBundle\DataProvider\DataSourceInterface;

interface DataHandlerInterface
{
    /**
     * Return true if the given data source is supported by the handler.
     */
    public function supports(DataSourceInterface $dataSource): bool;

    /**
     * Handle the given data source and transform it into a query result (a collection, a pager...).
     */
    public function handle(DataSourceInterface $dataSource);
}
