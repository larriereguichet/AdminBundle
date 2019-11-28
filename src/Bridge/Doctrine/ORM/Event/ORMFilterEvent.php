<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Event;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Filter\Filter;
use Symfony\Component\EventDispatcher\Event;

class ORMFilterEvent extends Event
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var AdminInterface
     */
    private $admin;

    /**
     * @var Filter[]
     */
    private $filters;

    /**
     * ORMFilterEvent constructor.
     */
    public function __construct(QueryBuilder $queryBuilder, AdminInterface $admin, array $filters = [])
    {
        $this->queryBuilder = $queryBuilder;
        $this->admin = $admin;
        $this->filters = $filters;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function getAdmin(): AdminInterface
    {
        return $this->admin;
    }

    /**
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }
}
