<?php

namespace LAG\AdminBundle\Bridge\Doctrine\Orm\DataProvider;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\DoctrineOrmFilterEvent;
use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\EventDispatcher\EventDispatcher;

class OrmDataProvider implements DataProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * DoctrineOrmDataProvider constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcher        $eventDispatcher
     */
    public function __construct(EntityManagerInterface $entityManager, EventDispatcher $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Load a collection of entities.
     *
     * @param AdminInterface $admin
     *
     * @return mixed
     */
    public function getCollection(AdminInterface $admin)
    {
        $queryBuilder = $this
            ->getRepository($admin->getConfiguration()->getParameter('entity'))
            ->createQueryBuilder('entity')
        ;
        $event = new DoctrineOrmFilterEvent($queryBuilder, $admin);
        $this->eventDispatcher->dispatch(AdminEvents::DOCTRINE_ORM_FILTER, $event);
        $entities = $queryBuilder->getQuery()->getResult();

        return $entities;
    }

    /**
     * Return a single entity.
     *
     * @param AdminInterface $admin
     * @param string         $identifier
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getItem(AdminInterface $admin, string $identifier)
    {
        $class = $admin->getConfiguration()->getParameter('entity');
        $item = $this
            ->getRepository($class)
            ->find($identifier)
        ;

        if (null === $item) {
            throw new Exception(sprintf(
                'Item of class "%s" with identifier "%s" not found.',
                $class,
                $identifier
            ));
        }

        return $item;
    }

    /**
     * Save an entity.
     *
     * @param AdminInterface $admin
     */
    public function saveItem(AdminInterface $admin)
    {
        $this->entityManager->persist($admin->getEntities()->first());
        $this->entityManager->flush();
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
