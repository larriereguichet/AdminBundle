<?php

namespace BlueBear\AdminBundle\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * GenericManager
 *
 * Use generic entity manager or provided custom entity manager methods
 */
class GenericManager
{

    protected $customManager;

    protected $entityRepository;

    /**
     * Doctrine entity manager
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $methodsMapping;

    /**
     * Initialize a generic manager with generic entity manager and optional custom manager
     *
     * @param EntityRepository $entityRepository
     * @param EntityManager $entityManager
     * @param null $customManager
     * @param array $methodsMapping
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
        $entity = new $entityNamespace;

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
     * @return QueryBuilder
     */
    public function getFindAllQueryBuilder()
    {
        return $this->entityRepository->createQueryBuilder('entity');
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