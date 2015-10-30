<?php

namespace LAG\AdminBundle\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * GenericManager.
 *
 * Use generic entity manager or provided custom entity manager methods
 */
class GenericManager
{
    protected $customManager;

    protected $entityRepository;

    /**
     * Doctrine entity manager.
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $methodsMapping;

    /**
     * Initialize a generic manager with generic entity manager and optional custom manager.
     *
     * @param EntityRepository $entityRepository
     * @param EntityManager    $entityManager
     * @param null             $customManager
     * @param array            $methodsMapping
     */
    public function __construct(EntityRepository $entityRepository, EntityManager $entityManager, $customManager = null, $methodsMapping = [])
    {
        $this->entityRepository = $entityRepository;
        $this->customManager = $customManager;
        $this->entityManager = $entityManager;
        $this->methodsMapping = $methodsMapping;
    }

    public function findOneBy($arguments = [])
    {
        if ($this->methodMatch('findOneBy')) {
            $method = $this->methodsMapping['findOneBy'];
            $entity = $this->customManager->$method($arguments);
        } else {
            $entity = $this->entityRepository->findOneBy($arguments);
        }

        return $entity;
    }

    public function findAll()
    {
        return [];
    }

    public function save($entity, $flush = true)
    {
        if ($this->methodMatch('save')) {
            $method = $this->methodsMapping['save'];
            $this->customManager->$method($entity, $flush);
        } else {
            $this->entityManager->persist($entity);

            if ($flush) {
                $this->entityManager->flush($entity);
            }
        }
    }

    public function create($entityNamespace)
    {
        $entity = new $entityNamespace();

        if ($this->methodMatch('create')) {
            $method = $this->methodsMapping['create'];
            $this->customManager->$method($entityNamespace);
        }

        return $entity;
    }

    public function delete($entity, $flush = true)
    {
        if ($this->methodMatch('delete')) {
            $method = $this->methodsMapping['delete'];
            $this->customManager->$method($entity);
        } else {
            $this->entityManager->remove($entity);

            if ($flush) {
                $this->entityManager->flush($entity);
            }
        }
    }

    /**
     * Return query builder to find all entities, with optional order.
     *
     * @param string $sort
     * @param string $order
     *
     * @return QueryBuilder
     */
    public function getFindAllQueryBuilder($sort = null, $order = 'ASC')
    {
        $queryBuilder = $this
            ->entityRepository
            ->createQueryBuilder('entity');
        // @TODO sort seems to not working with entity from FOSUser. It's maybe a bug in Doctrine or FOSUserBundle
        if (in_array('FOS\UserBundle\Model\UserInterface', class_implements($this->entityRepository->getClassName()))) {
            return $queryBuilder;
        }
        // sort is optional
        if ($sort) {
            // check in metadata if sort column is a relation
            $metadata = $this->entityManager->getMetadataFactory()->getMetadataFor($this->entityRepository->getClassName());
            $orderDql = 'entity.'.$sort;

            if (in_array($sort, $metadata->getAssociationNames())) {
                $queryBuilder
                    ->addSelect($sort)
                    ->join('entity.'.$sort, $sort);
                // sort by name by default
                $orderDql = $sort.'.name';
            }
            $queryBuilder->orderBy($orderDql, $order);
        }

        return $queryBuilder;
    }

    public function getCountQueryBuilderCallback()
    {
        $callback = function (QueryBuilder $queryBuilder) {

        };

        return $callback;
    }

    protected function methodMatch($method)
    {
        return array_key_exists($method, $this->methodsMapping);
    }
}
