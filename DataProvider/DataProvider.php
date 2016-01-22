<?php

namespace LAG\AdminBundle\DataProvider;

use Doctrine\ORM\EntityManagerInterface;
use LAG\DoctrineRepositoryBundle\Repository\RepositoryInterface;

class DataProvider implements DataProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repositoryInterface;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManagerInterface;

    public function __construct(
        RepositoryInterface $repositoryInterface,
        EntityManagerInterface $entityManagerInterface
    ) {
        $this->repositoryInterface = $repositoryInterface;
        $this->entityManagerInterface = $entityManagerInterface;
    }

    public function save($entity)
    {
        // TODO: Implement save() method.
    }

    public function delete($entity)
    {
        // TODO: Implement delete() method.
    }

    public function find(array $criteria, $orderBy = [], $limit = null, $offset = null)
    {
        // TODO: Implement find() method.
    }
}
