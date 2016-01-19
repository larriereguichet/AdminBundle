<?php

namespace LAG\AdminBundle\Repository;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use LAG\DoctrineRepositoryBundle\Repository\DoctrineRepository;

/**
 * Generic implementation of DoctrineRepository abstract class
 */
class GenericRepository extends DoctrineRepository
{
    /**
     * GenericRepository constructor.
     *
     * @param ObjectManager $objectManager
     * @param ObjectRepository $repository
     * @param $entityClassName
     */
    public function __construct(ObjectManager $objectManager, ObjectRepository $repository, $entityClassName)
    {
        $this->objectManager = $objectManager;
        $this->repository = $repository;
        $this->entityClassName = $entityClassName;
    }
}
