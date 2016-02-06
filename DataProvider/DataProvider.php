<?php

namespace LAG\AdminBundle\DataProvider;

use Doctrine\Common\Collections\Collection;
use LAG\DoctrineRepositoryBundle\Repository\RepositoryInterface;

/**
 * Default data provider using generic repositories
 */
class DataProvider implements DataProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * DataProvider constructor.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(
        RepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * Find entities by criteria
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return Collection
     */
    public function findBy(array $criteria = [], $orderBy = [], $limit = null, $offset = null)
    {
        return $this
            ->repository
            ->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Find an entity by its unique id
     *
     * @param $id
     * @return object
     */
    public function find($id)
    {
        return $this
            ->repository
            ->find($id);
    }

    /**
     * Save an entity
     *
     * @param $entity
     */
    public function save($entity)
    {
        $this
            ->repository
            ->save($entity);
    }

    /**
     * Remove an entity
     *
     * @param $entity
     */
    public function remove($entity)
    {
        $this
            ->repository
            ->delete($entity);
    }
}
