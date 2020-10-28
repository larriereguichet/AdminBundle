<?php

namespace LAG\AdminBundle\Bridge\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Results\ResultsHandlerInterface;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\UnexpectedTypeException;
use LAG\AdminBundle\Filter\FilterInterface;

class ORMDataProvider implements DataProviderInterface
{
    private EntityManagerInterface $entityManager;
    private ResultsHandlerInterface $resultsHandler;
    private AdminHelperInterface $adminHelper;

    public function __construct(
        EntityManagerInterface $entityManager,
        AdminHelperInterface $adminHelper,
        ResultsHandlerInterface $handler
    ) {
        $this->entityManager = $entityManager;
        $this->resultsHandler = $handler;
        $this->adminHelper = $adminHelper;
    }

    public function getCollection(
        string $class,
        array $criteria = [],
        array $orderBy = [],
        int $limit = 1,
        int $offset = 25
    ): object {
        $admin = $this->getAdmin();
        $adminConfiguration = $admin->getConfiguration();
        $actionConfiguration = $admin->getAction()->getConfiguration();
        $repository = $this->getRepository($adminConfiguration->getEntityClass());

        // Allow to change the default method in configuration
        $method = $actionConfiguration->getRepositoryMethod();
        $pagination = ('pagerfanta' === $actionConfiguration->getPager());

        // Fetch pagination parameters
        $pageParameter = $actionConfiguration->getPageParameter();
        $page = (int) $admin->getRequest()->get($pageParameter, 1);
        $maxPerPage = $actionConfiguration->getMaxPerPage();

        // The repository could return an object, an array, a collection, a pager or a query builder. The results
        // handler will act according to result type
        if ($method) {
            if (!method_exists($repository, $method)) {
                throw new Exception(sprintf('The method "%s" does not exists for the class "%s"', $method, get_class($repository)));
            }
            $data = $repository->$method($criteria, $orderBy, $limit, $offset);
        } else {
            $data = $repository->createQueryBuilder('entity');
            $this->addFilters($data, $criteria);
            $this->addOrderBy($data, $orderBy);
        }

        return $this->resultsHandler->handle($data, $pagination, $page, $maxPerPage);
    }

    public function get(string $class, $identifier): object
    {
        $item = $this
            ->getRepository($class)
            ->find($identifier)
        ;

        if ($item === null) {
            throw new Exception(sprintf('Item of class "%s" with identifier "%s" not found.', $class, $identifier));
        }

        return $item;
    }

    public function create(string $class): object
    {
        return new $class();
    }

    private function getRepository(string $class): ObjectRepository
    {
        $repository = $this->entityManager->getRepository($class);
        $admin = $this->adminHelper->getAdmin();

        if (!$repository instanceof EntityRepository) {
            throw new Exception(sprintf('The repository of admin "%s" should be an instance of "%s" to use the default method createQueryBuilder()', $admin->getName(), EntityRepository::class));
        }

        return $repository;
    }

    private function getAdmin(): AdminInterface
    {
        return $this->adminHelper->getAdmin();
    }

    private function addFilters(QueryBuilder $queryBuilder, array $criteria): void
    {
        foreach ($criteria as $criterion) {
            if (!$criterion instanceof FilterInterface) {
                throw new UnexpectedTypeException($criterion, FilterInterface::class);
            }
            $alias = $queryBuilder->getRootAliases()[0];
            $parameterName = 'filter_'.$criterion->getName();
            $value = $criterion->getValue();

            if ('like' === $criterion->getComparator()) {
                $value = '%'.$value.'%';
            }

            if ('and' === $criterion->getOperator()) {
                $method = 'andWhere';
            } else {
                $method = 'orWhere';
            }

            $queryBuilder->$method(sprintf(
                '%s.%s %s %s',
                $alias,
                $criterion->getName(),
                $criterion->getComparator(),
                ':'.$parameterName
            ));
            $queryBuilder->setParameter($parameterName, $value);
        }
    }

    private function addOrderBy(QueryBuilder $queryBuilder, array $order): void
    {
        foreach ($order as $field => $orderValue) {
            $alias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->addOrderBy($alias.'.'.$field, $orderValue);
        }
    }
}
