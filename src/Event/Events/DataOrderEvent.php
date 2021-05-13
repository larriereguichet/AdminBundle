<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\DataProvider\DataSourceInterface;

class DataOrderEvent
{
    private AdminInterface $admin;
    private DataSourceInterface $dataSource;
    private array $orderBy;

    public function __construct(AdminInterface $admin, DataSourceInterface $dataSource, array $orderBy)
    {
        $this->admin = $admin;
        $this->dataSource = $dataSource;
        $this->orderBy = $orderBy;
    }

    public function getAdmin(): AdminInterface
    {
        return $this->admin;
    }

    public function getDataSource(): DataSourceInterface
    {
        return $this->dataSource;
    }

    public function getOrderBy(): array
    {
        return $this->orderBy;
    }
}
