<?php

namespace LAG\AdminBundle\Event;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Admin\AdminInterface;
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
     * DoctrineOrmFilterEvent constructor.
     *
     * @param QueryBuilder   $queryBuilder
     * @param AdminInterface $admin
     */
    public function __construct(QueryBuilder $queryBuilder, AdminInterface $admin)
    {
        $this->queryBuilder = $queryBuilder;
        $this->admin = $admin;
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
}
