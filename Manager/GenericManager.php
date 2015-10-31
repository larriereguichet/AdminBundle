<?php

namespace LAG\AdminBundle\Manager;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Admin\ManagerInterface;

/**
 * GenericManager.
 *
 * Use generic entity manager or provided custom entity manager methods
 */
class GenericManager implements ManagerInterface
{
    /**
     * @var ObjectRepository|EntityRepository
     */
    protected $entityRepository;

    /**
     * Doctrine entity manager.
     *
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $manager, ObjectRepository $repository)
    {
        $this->entityManager = $manager;
        $this->entityRepository = $repository;
    }

    public function findOneBy(array $criteria)
    {
        $entity = $this
            ->entityRepository
            ->findOneBy($criteria);

        return $entity;
    }

    public function findAll()
    {
        $entities = $this
            ->entityRepository
            ->findAll();

        return $entities;
    }

    public function save($entity, $flush = true)
    {
        $this->entityManager->persist($entity);

        if ($flush) {
            $this->entityManager->flush($entity);
        }
    }

    public function create()
    {
        $className = $this->getClassName();
        $entity = new $className();

        return $entity;
    }

    public function delete($entity, $flush = true)
    {
        $this->entityManager->remove($entity);

        if ($flush) {
            $this->entityManager->flush($entity);
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
            $orderDql = 'entity.' . $sort;

            if (in_array($sort, $metadata->getAssociationNames())) {
                $queryBuilder
                    ->addSelect($sort)
                    ->join('entity.' . $sort, $sort);
                // sort by name by default
                $orderDql = $sort . '.name';
            }
            $queryBuilder->orderBy($orderDql, $order);
        }

        return $queryBuilder;
    }

    /**
     * Finds an object by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     *
     * @return object The object.
     */
    public function find($id)
    {
        // TODO: Implement find() method.
    }

    /**
     * Finds objects by a set of criteria.
     *
     * Optionally sorting and limiting details can be passed. An implementation may throw
     * an UnexpectedValueException if certain values of the sorting or limiting details are
     * not supported.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array The objects.
     *
     * @throws \UnexpectedValueException
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        // TODO: Implement findBy() method.
    }

    /**
     * Returns the class name of the object managed by the manager.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this
            ->entityRepository
            ->getClassName();
    }
}
