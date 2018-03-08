<?php

namespace LAG\AdminBundle\DataProvider;

use Doctrine\Common\Persistence\ObjectRepository;
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
        $queryBuilder = $this
            ->getRepository($entityClass)
            ->createQueryBuilder('entity')
        ;

        $entities = $queryBuilder->getQuery()->getResult();

        return $entities;
    }

    public function getItem(string $entityClass, $identifier)
    {
        $item = $this
            ->getRepository($entityClass)
            ->find($identifier)
        ;
    }

    /**
     * @param string $entityClass
     *
     * @return EntityRepository|ObjectRepository
     */
    private function getRepository(string $entityClass)
    {
        return $this->entityManager->getRepository($entityClass);
    }
}
