<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\DataProvider\DataSourceInterface;
use Symfony\Contracts\EventDispatcher\Event;

class DataFilterEvent extends Event
{
    private AdminInterface $admin;
    private DataSourceInterface $dataSource;
    private array $filters;

    public function __construct(AdminInterface $admin, DataSourceInterface $dataSource, array $filters)
    {
        $this->admin = $admin;
        $this->dataSource = $dataSource;
        $this->filters = $filters;
    }

    public function getAdmin(): AdminInterface
    {
        return $this->admin;
    }

    public function getDataSource(): DataSourceInterface
    {
        return $this->dataSource;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}
