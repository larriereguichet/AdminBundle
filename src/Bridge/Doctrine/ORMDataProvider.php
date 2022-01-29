<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Bridge\Doctrine\DataSource\ORMDataSource;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\DataProvider\DataSourceInterface;
use LAG\AdminBundle\Exception\Exception;

class ORMDataProvider implements DataProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AdminHelperInterface $adminHelper
    ) {
    }

    public function getCollection(
        string $class,
        array $criteria = [],
        array $orderBy = [],
        int $limit = 1,
        int $offset = 25
    ): DataSourceInterface {
        $admin = $this->getAdmin();
        $adminConfiguration = $admin->getConfiguration();
        $actionConfiguration = $admin->getAction()->getConfiguration();
        $repository = $this->getRepository($adminConfiguration->getEntityClass());

        // Allow to change the default method in configuration
        $method = $actionConfiguration->getRepositoryMethod();
        $isPaginated = ('pagerfanta' === $actionConfiguration->getPager());

        // Fetch pagination parameters
        $pageParameter = $actionConfiguration->getPageParameter();
        $page = (int) $admin->getRequest()->get($pageParameter, 1);
        $maxPerPage = $actionConfiguration->getMaxPerPage();

        // The repository could return an object, an array, a collection, a pager or a query builder. The results
        // handler will act according to result type
        if ($method) {
            if (!method_exists($repository, $method)) {
                throw new Exception(sprintf('The method "%s" does not exists for the class "%s"', $method, \get_class($repository)));
            }
            $data = $repository->$method($criteria, $orderBy, $limit, $offset);

            if (!$data instanceof QueryBuilder) {
                throw new Exception(sprintf('The method "%s" of the repository "%s" should return a instance of "%s"', $method, \get_class($repository), QueryBuilder::class));
            }
        } else {
            $data = $repository->createQueryBuilder('entity');
        }

        return new ORMDataSource($data, $isPaginated, $page, $maxPerPage);
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

    private function getRepository(string $class): EntityRepository
    {
        $repository = $this->entityManager->getRepository($class);

        if (!$repository instanceof EntityRepository) {
            $admin = $this->adminHelper->getAdmin();
            throw new Exception(sprintf('The repository of admin "%s" should be an instance of "%s" to use the default method createQueryBuilder()', $admin->getName(), EntityRepository::class));
        }

        return $repository;
    }

    private function getAdmin(): AdminInterface
    {
        return $this->adminHelper->getAdmin();
    }
}
