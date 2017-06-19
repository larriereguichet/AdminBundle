<?php

namespace LAG\AdminBundle\DataProvider;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
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
     *
     * @return array|Collection
     */
    public function findBy(array $criteria = [], $orderBy = [], $limit = null, $offset = null)
    {
        if ($this->repository instanceof EntityRepository) {
            $queryBuilder = $this
                ->repository
                ->createQueryBuilder('entity')
            ;
    
            foreach ($criteria as $criterion => $value) {
                $comparison = ' = ';
    
                if ($this->isWildCardValue($value)) {
                    $comparison = ' like ';
                }
                $parameter = ':'.$criterion;
                
                $queryBuilder
                    ->andWhere('entity.'.$criterion.$comparison.$parameter)
                    ->setParameter($parameter, $value)
                ;
            }
    
            foreach ($orderBy as $sort => $order) {
                $queryBuilder
                    ->addOrderBy('entity.'.$sort, $order)
                ;
            }
            $entities = $queryBuilder
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult()
            ;
        } else {
            $entities = $this
                ->repository
                ->findBy($criteria, $orderBy, $limit, $offset)
            ;
        }
    
        return $entities;
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
    
    private function isWildCardValue($value)
    {
        if ('%' !== substr($value, 0, 1)) {
            return false;
        }
        
        if ('%' !== substr($value, -1, 1)) {
            return false;
        }
        
        return true;
    }
}
