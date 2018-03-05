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
     * DoctrineOrmDataProvider constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getCollection(string $entityClass)
    {
        /** @var EntityRepository $repository */
        $repository = $this->entityManager->getRepository($entityClass);
        $queryBuilder = $repository->createQueryBuilder('entity');

        $entities = $queryBuilder->getQuery()->getResult();

        return $entities;
    }
}
