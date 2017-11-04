<?php

namespace LAG\AdminBundle\DataProvider;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
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
     * @var EntityManagerInterface
     */
    protected $entityManager;
    
    /**
     * DataProvider constructor.
     *
     * @param RepositoryInterface    $repository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(RepositoryInterface $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
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
            $metadata = $this
                ->entityManager
                ->getClassMetadata($this->repository->getClassName())
            ;
    
    
            foreach ($orderBy as $sort => $order) {
                
                if ($metadata->hasAssociation($sort)) {
                    // TODO get id dynamically
                    $queryBuilder
                        ->leftJoin('entity.'.$sort, $sort)
                        ->addSelect('COUNT('.$sort.'.id) AS HIDDEN '.$sort.'_count')
                        ->addOrderBy($sort.'_count', $order)
                        ->addGroupBy($sort.'.id')
                    ;
                } else {
                    $queryBuilder
                        ->addOrderBy('entity.'.$sort, $order)
                    ;
                }
                // TODO fix bug with association
            }
            $entities = $queryBuilder
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->getQuery()
                ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, [])
                ->setHint(Query::HINT_CUSTOM_TREE_WALKERS, [])
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
