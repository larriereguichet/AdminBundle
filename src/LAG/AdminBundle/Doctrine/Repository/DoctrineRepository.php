<?php

namespace LAG\AdminBundle\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Repository\RepositoryInterface;

/**
 * Abstract doctrine repository
 */
abstract class DoctrineRepository extends EntityRepository implements RepositoryInterface
{
    /**
     * Save an entity.
     *
     * @param $entity
     */
    public function save($entity)
    {
        $this
            ->_em
            ->persist($entity)
        ;
        $this
            ->_em
            ->flush()
        ;
    }
    /**
     * Delete an entity.
     *
     * @param $entity
     */
    public function delete($entity)
    {
        $this
            ->_em
            ->remove($entity)
        ;
        $this
            ->_em
            ->flush()
        ;
    }
    
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, array $options = [])
    {
        $queryBuilder = $this->createQueryBuilder('entity');
        
        $this->addCriteria($queryBuilder, $criteria, $options);
    
        if (null !== $orderBy) {
            foreach ($orderBy as $sort => $order) {
                $queryBuilder
                    ->addOrderBy('entity.'.$sort, $order)
                ;
            }
        }
    
        if (null !== $limit) {
            $queryBuilder->setMaxResults($limit);
        }
    
        if (null !== $offset) {
            $queryBuilder->setFirstResult($offset);
        }
    
        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }
    
    /**
     * @inheritdoc
     */
    public function count(array $criteria = [], array $options = [])
    {
        $identifiers = $this
            ->getClassMetadata()
            ->getIdentifierColumnNames()
        ;
        $pieces = [];
    
        foreach ($identifiers as $identifier) {
            $pieces[] = 'entity.'.$identifier;
        }
        
        return $this
            ->createQueryBuilder('entity')
            ->select('count('.implode(',', $pieces).')')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
    
    /**
     * Add criteria values from options.
     *
     * @param QueryBuilder $queryBuilder
     * @param array        $criteria
     * @param array        $options
     */
    private function addCriteria(QueryBuilder $queryBuilder, array $criteria, array $options)
    {
        foreach ($criteria as $criterion => $value) {
            // default values
            $operator = '=';
            
            if (array_key_exists($criterion, $options)) {
    
                if (array_key_exists('operator', $options[$criterion])) {
                    $operator = $options[$criterion]['operator'];
                }
            }
    
            if ('LIKE' === $operator) {
                $value = '%'.$value.'%';
            }
            $whereString = sprintf(
                '%s %s :%s',
                'entity.'.$criterion,
                $operator,
                $criterion.'_value'
            );
            
            $queryBuilder
                ->andWhere($whereString)
                ->setParameter($criterion.'_value', $value)
            ;
        }
    }
}
