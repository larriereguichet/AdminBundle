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
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class OrmDataProvider implements DataProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * DoctrineOrmDataProvider constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param RequestStack $requestStack
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        RequestStack $requestStack
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
    }

    /**
     * Load a collection of entities.
     *
     * @param AdminInterface $admin
     * @param array          $filters
     *
     * @return mixed
     */
    public function getCollection(AdminInterface $admin, array $filters = [])
    {
        $queryBuilder = $this
            ->getRepository($admin->getConfiguration()->getParameter('entity'))
            ->createQueryBuilder('entity')
        ;
        $event = new DoctrineOrmFilterEvent($queryBuilder, $admin, $filters);
        $this->eventDispatcher->dispatch(AdminEvents::DOCTRINE_ORM_FILTER, $event);
        $configuration = $admin->getConfiguration();
        $entities = null;

        if ('pagerfanta' === $configuration->getParameter('pager')) {
            $pageParameter = $configuration->getParameter('page_parameter');
            $request = $this->requestStack->getCurrentRequest();
            $page = (int)$request->get($pageParameter, 1);

            $adapter = new DoctrineORMAdapter($queryBuilder);
            $pager = new Pagerfanta($adapter);
            $pager->setCurrentPage($page);
            $pager->setMaxPerPage($configuration->getParameter('max_per_page'));
            $entities = $pager;
        } else {
            $entities = $queryBuilder->getQuery()->getResult();
        }

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
            ->find($identifier);

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
