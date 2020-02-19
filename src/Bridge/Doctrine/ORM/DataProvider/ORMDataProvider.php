<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\DataProvider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Event\ORMFilterEvent;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Results\ResultsHandlerInterface;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Exception\Exception;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ORMDataProvider implements DataProviderInterface
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
     * @var ResultsHandlerInterface
     */
    private $resultsHandler;

    /**
     * DoctrineORMDataProvider constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        ResultsHandlerInterface $handler
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->resultsHandler = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection(AdminInterface $admin, array $filters = [])
    {
        $adminConfiguration = $admin->getConfiguration();
        $actionConfiguration = $admin->getAction()->getConfiguration();
        $repository = $this->getRepository($adminConfiguration->get('entity'));

        // Allow to change the default method in configuration
        $alias = strtolower($admin->getName());
        $method = $actionConfiguration->get('repository_method');
        $pagination = ('pagerfanta' === $actionConfiguration->get('pager'));

        // The repository could return an object, an array, a collection, a pager or a query builder. The results
        // handler will act according to result type
        if ($method) {
            $data = $repository->$method($alias);
        } else {
            if (!$repository instanceof EntityRepository) {
                throw new Exception(sprintf('The repository of admin "%s" should be an instance of "%s" to use the default method createQueryBuilder()', $admin->getName(), EntityRepository::class));
            }
            $data = $repository->createQueryBuilder($alias);
        }

        // Dispatch an event to allow filter alteration on the query builder
        $event = new ORMFilterEvent($data, $admin, $filters);
        $this->eventDispatcher->dispatch($event, Events::DOCTRINE_ORM_FILTER);

        // Fetch pagination parameters
        $pageParameter = $actionConfiguration->get('page_parameter');
        $page = (int) $admin->getRequest()->get($pageParameter, 1);
        $maxPerPage = $actionConfiguration->get('max_per_page');

        return $this->resultsHandler->handle($data, $pagination, $page, $maxPerPage);
    }

    /**
     * {@inheritdoc}
     */
    public function get(AdminInterface $admin, string $identifier)
    {
        $class = $admin->getConfiguration()->get('entity');
        $item = $this
            ->getRepository($class)
            ->find($identifier)
        ;

        if (null === $item) {
            throw new Exception(sprintf('Item of class "%s" with identifier "%s" not found.', $class, $identifier));
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function save(AdminInterface $admin): void
    {
        $this->entityManager->persist($admin->getEntities()->first());
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function create(AdminInterface $admin)
    {
        $class = $admin->getConfiguration()->get('entity');

        return new $class();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AdminInterface $admin): void
    {
        if ($admin->getEntities()->isEmpty()) {
            throw new Exception('The admin "'.$admin->getName().'" has no loaded entity');
        }
        $this->entityManager->remove($admin->getEntities()->first());
        $this->entityManager->flush();
    }

    /**
     * @return ObjectRepository|EntityRepository
     */
    private function getRepository(string $class)
    {
        return $this->entityManager->getRepository($class);
    }
}
