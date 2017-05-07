<?php

namespace LAG\AdminBundle\DataProvider;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\Repository\RepositoryInterface;

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
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * Find entities by criteria.
     *
     * @param array    $criteria
     * @param array    $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @param array    $options
     *
     * @return array|Collection
     */
    public function findBy(array $criteria = [], $orderBy = [], $limit = null, $offset = null, array $options = [])
    {
        return $this
            ->repository
            ->findBy($criteria, $orderBy, $limit, $offset, $options)
        ;
    }

    /**
     * Find an entity by its unique id.
     *
     * @param $id
     * @return object
     */
    public function find($id)
    {
        return $this
            ->repository
            ->find($id)
        ;
    }

    /**
     * Save an entity.
     *
     * @param $entity
     */
    public function save($entity)
    {
        $this
            ->repository
            ->save($entity)
        ;
    }

    /**
     * Remove an entity.
     *
     * @param $entity
     */
    public function remove($entity)
    {
        $this
            ->repository
            ->delete($entity)
        ;
    }

    /**
     * Create a new entity.
     *
     * @return object
     */
    public function create()
    {
        $className = $this
            ->repository
            ->getClassName()
        ;

        return new $className;
    }
    
    /**
     * Return the number of entities in the Repository.
     *
     * @param array $criteria
     * @param array $options
     *
     * @return int
     */
    public function count(array $criteria = [], array $options = [])
    {
        return $this
            ->repository
            ->count($criteria, $options)
        ;
    }
}
