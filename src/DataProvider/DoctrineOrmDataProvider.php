<?php

namespace LAG\AdminBundle\DataProvider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineOrmDataProvider implements DataProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * DoctrineOrmDataProvider constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param string                 $entityClass
     */
    public function __construct(EntityManagerInterface $entityManager, $entityClass)
    {
        $this->entityManager = $entityManager;
        $this->entityClass = $entityClass;
        $this->repository = $entityManager->getRepository($entityClass);
    }

    /**
     * Save an entity.
     *
     * @param $entity
     */
    public function save($entity)
    {
        // TODO: Implement save() method.
    }

    /**
     * Remove an entity.
     *
     * @param $entity
     */
    public function remove($entity)
    {
        // TODO: Implement remove() method.
    }

    /**
     * Create an new entity.
     *
     * @return mixed
     */
    public function create()
    {
        // TODO: Implement create() method.
    }

    /**
     * Find entities according to the given criteria.
     *
     * @param array    $criteria
     * @param array    $orderBy
     * @param null|int $limit
     * @param null|int $offset
     *
     * @return mixed
     */
    public function findBy(array $criteria = [], $orderBy = [], $limit = null, $offset = null)
    {
        // TODO: Implement findBy() method.
    }

    /**
     * Find an entity according to its unique id.
     *
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        // TODO: Implement find() method.
    }

    /**
     * Return the total number of entities managed by the DataProvider.
     *
     * @param array $criteria
     *
     * @param array $options
     *
     * @return int
     */
    public function count(array $criteria = [], array $options = [])
    {
        // TODO: Implement count() method.
    }
}
