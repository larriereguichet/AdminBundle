<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Registry;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\State\DataProviderInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class ORMDataProvider implements DataProviderInterface
{
    public function __construct(
        private Registry $registry,
    ) {
    }

    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): mixed
    {
        $manager = $this->registry->getManagerForClass($operation->getResource()->getDataClass());

        if ($manager === null) {
            throw new EntityManagerNotFoundException($operation);
        }
        $repository = $manager->getRepository($operation->getResource()->getDataClass());

        if ($operation instanceof Create) {
            $class = $operation->getResource()->getDataClass();
            return new $class();
        }

        if ($operation instanceof CollectionOperationInterface) {
            $queryBuilder = $repository
                ->createQueryBuilder('entity')
                // TODO order by
                //->orderBy($operation->getOrderBy())
            ;

            if (!$operation->hasPagination()) {
                return $queryBuilder->getQuery()->getResult();
            }
            $pager = new Pagerfanta(new QueryAdapter($queryBuilder, true));
            $pager->setMaxPerPage($operation->getItemPerPage());
            $pager->setCurrentPage($context['page'] ?? 1);

            return $pager;
        }
        $queryBuilder = $repository->createQueryBuilder('entity');

        foreach ($operation->getIdentifiers() as $identifier) {
            if ($uriVariables[$identifier] ?? false) {
                $queryBuilder->andWhere(sprintf('entity.%s = %s', $identifier, $uriVariables[$identifier]));
            }
        }

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    public function getCollection(
//        string $class,
//        array $criteria = [],
//        array $orderBy = [],
//        int $limit = 1,
//        int $offset = 25
//    ): DataSourceInterface {
//        $admin = $this->getAdmin();
//        $adminConfiguration = $admin->getConfiguration();
//        $actionConfiguration = $admin->getAction()->getConfiguration();
//        $repository = $this->getRepository($adminConfiguration->getEntityClass());
//
//        // Allow to change the default method in configuration
//        $method = $actionConfiguration->getRepositoryMethod();
//        $isPaginated = ('pagerfanta' === $actionConfiguration->getPager());
//
//        // Fetch pagination parameters
//        $pageParameter = $actionConfiguration->getPageParameter();
//        $page = (int) $admin->getRequest()->get($pageParameter, 1);
//        $maxPerPage = $actionConfiguration->getMaxPerPage();
//
//        // The repository could return an object, an array, a collection, a pager or a query builder. The results
//        // handler will act according to result type
//        if ($method) {
//            if (!method_exists($repository, $method)) {
//                throw new Exception(sprintf('The method "%s" does not exists for the class "%s"', $method, \get_class($repository)));
//            }
//            $data = $repository->$method($criteria, $orderBy, $limit, $offset);
//
//            if (!$data instanceof QueryBuilder) {
//                throw new Exception(sprintf('The method "%s" of the repository "%s" should return a instance of "%s"', $method, \get_class($repository), QueryBuilder::class));
//            }
//        } else {
//            $data = $repository->createQueryBuilder('entity');
//        }
//
//        return new ORMDataSource($data, $isPaginated, $page, $maxPerPage);
//    }
//
//    public function get(string $class, $identifier): object
//    {
//        $item = $this
//            ->getRepository($class)
//            ->find($identifier)
//        ;
//
//        if ($item === null) {
//            throw new Exception(sprintf('Item of class "%s" with identifier "%s" not found.', $class, $identifier));
//        }
//
//        return $item;
//    }
//
//    public function create(string $class): object
//    {
//        return new $class();
//    }
//
//    private function getRepository(string $class): EntityRepository
//    {
//        $repository = $this->entityManager->getRepository($class);
//
//        if (!$repository instanceof EntityRepository) {
//            $admin = $this->adminHelper->getAdmin();
//            throw new Exception(sprintf('The repository of admin "%s" should be an instance of "%s" to use the default method createQueryBuilder()', $admin->getName(), EntityRepository::class));
//        }
//
//        return $repository;
//    }
//
//    private function getAdmin(): AdminInterface
//    {
//        return $this->adminHelper->getAdmin();
//    }
}
