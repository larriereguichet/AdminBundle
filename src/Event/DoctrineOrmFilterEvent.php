<?php

namespace LAG\AdminBundle\Event;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Filter\Filter;
use Symfony\Component\EventDispatcher\Event;

class DoctrineOrmFilterEvent extends Event
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
     * DoctrineOrmFilterEvent constructor.
     *
     * @param QueryBuilder   $queryBuilder
     * @param AdminInterface $admin
     * @param array          $filters
     */
    public function __construct(QueryBuilder $queryBuilder, AdminInterface $admin, array $filters = [])
    {
        $this->queryBuilder = $queryBuilder;
        $this->admin = $admin;
        $this->filters = $filters;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    /**
     * @return AdminInterface
     */
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
