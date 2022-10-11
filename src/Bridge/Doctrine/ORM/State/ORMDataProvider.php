<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use LAG\AdminBundle\Bridge\Doctrine\DataSource\ORMDataSource;
use LAG\AdminBundle\Bridge\Doctrine\ORM\EntityManagerNotFoundException;
use LAG\AdminBundle\Bridge\Doctrine\ORM\QueryBuilder\QueryBuilderHelper;
use LAG\AdminBundle\Event\Events\DataOrderEvent;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\State\DataProviderInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use function Symfony\Component\String\u;

class ORMDataProvider implements DataProviderInterface
{
    public function __construct(
        private Registry $registry,
    ) {
    }

    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Create) {
            $class = $operation->getResource()->getDataClass();

            return new $class();
        }
        $manager = $this->registry->getManagerForClass($operation->getResource()->getDataClass());

        if ($manager === null) {
            throw new EntityManagerNotFoundException($operation);
        }
        /** @var EntityRepository $repository */
        $repository = $manager->getRepository($operation->getResource()->getDataClass());
        // Add a suffix to avoid error if the resource is named with a reserved keyword
        $rootAlias = $operation->getResourceName().'_entity';

        $queryBuilder = $repository->createQueryBuilder($rootAlias);
        $helper = new QueryBuilderHelper(
            $queryBuilder,
            $manager->getClassMetadata($operation->getResource()->getDataClass()),
        );

        if ($operation instanceof CollectionOperationInterface) {
            $orderBy = $operation->getOrderBy();

            if (($context['sort'] ?? false) && ($context['order'] ?? false)) {
                $orderBy[$context['sort']] = $context['order'];
            }
            $helper->addOrderBy($orderBy);
            $filters = [];

            foreach ($operation->getFilters() as $filter) {
                $data = $context['filters'][$filter->getName()] ?? null;

                if ($data) {
                    $filters[] = $filter->withData($data);
                }
            }
            $helper->addFilters($filters);

            if (!$operation->hasPagination()) {
                return $helper
                    ->getQueryBuilder()
                    ->getQuery()
                    ->getResult()
                ;
            }
            $pager = new Pagerfanta(new QueryAdapter($helper->getQueryBuilder(), true));
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

    private function createCollectionQueryBuilder(
        ObjectRepository $repository,
        CollectionOperationInterface $operation,
        array $values,
    ): QueryBuilder {
        $queryBuilder = $repository
            ->createQueryBuilder('entity')
        ;

        foreach ($operation->getOrderBy() as $sort => $order) {
            $queryBuilder->addOrderBy($sort, $order);
        }

        foreach ($values as $filterName => $value) {
            $filter = $operation->getFilter($filterName);
            $method = $filter->getOperator() === 'and' ? 'andWhere' : 'orWhere';
            $value = $values[$filter->getName()];

            // Do not filter on null values
            if ($value === null) {
                continue;
            }

            if ('between' === $filter->getComparator()) {
                $parameterName1 = 'filter_'.u($filter->getPropertyPath())->snake()->toString().'_1';
                $parameterName2 = 'filter_'.u($filter->getPropertyPath())->snake()->toString().'_2';

                $dql = sprintf(
                    'entity.%s > :%s and entity.%s < :%s',
                    $filter->getPropertyPath(),
                    $parameterName1,
                    $filter->getPropertyPath(),
                    $parameterName2
                );

                $queryBuilder->$method($dql);
                $queryBuilder->setParameter($parameterName1, $value);
                $queryBuilder->setParameter($parameterName2, $value);

                return $queryBuilder;
            }

            if ($filter->getComparator() === 'like') {
                $value = '%'.$value.'%';
            }
            $parameterName = 'filter_'.u($filter->getName())->snake()->toString();

            $dql = sprintf(
                '%s.%s %s :%s',
                'entity',
                $filter->getName(),
                $filter->getComparator(),
                $parameterName
            );
            $queryBuilder->$method($dql);
            $queryBuilder->setParameter($parameterName, $value);
        }

        return $queryBuilder;
    }

    private function addAssociationFilter(QueryBuilder $queryBuilder, FilterInterface $criterion): void
    {
        $alias = $queryBuilder->getRootAliases()[0];
        $value = $criterion->getValue();
        $parameterName = 'filter_'.u($criterion->getName())->snake()->toString();

        $joinDql = sprintf('%s.%s', $alias, $criterion->getName());
        $whereDql = sprintf(
            '%s = :%s',
            $criterion->getName(),
            $parameterName
        );

        $queryBuilder
            ->innerJoin($joinDql, $criterion->getName())
            ->andWhere($whereDql)
            ->setParameter($parameterName, $value)
        ;
    }

    public function __invoke(DataOrderEvent $event): void
    {
        $dataSource = $event->getDataSource();

        if (!$dataSource instanceof ORMDataSource) {
            return;
        }
        $order = $event->getOrderBy();
        $queryBuilder = $event->getDataSource()->getData();
        $queryBuilder->resetDQLPart('orderBy');
        $metadata = $this->entityManager->getClassMetadata($event->getAdmin()->getEntityClass());

        foreach ($order as $field => $orderValue) {
            if ($metadata->hasField($field)) {
                $this->addFieldOrder($queryBuilder, $field, $orderValue);
            }

            if ($metadata->hasAssociation($field)) {
                $this->addAssociationOrder($queryBuilder, $metadata, $field, $orderValue);
            }
        }
    }

    private function addFieldOrder(QueryBuilder $queryBuilder, string $field, string $orderValue): void
    {
        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->addOrderBy($alias.'.'.$field, $orderValue);
    }

    private function addAssociationOrder(QueryBuilder $queryBuilder, ClassMetadata $metadata, string $field, string $orderValue): void
    {
        $joins = $queryBuilder->getDQLPart('join');
        $alias = $queryBuilder->getRootAliases()[0];

        $joinAlias = null;

        /** @var Join[] $rootJoins */
        foreach ($joins as $rootEntityJoin => $rootJoins) {
            if ($rootEntityJoin !== $alias) {
                continue;
            }
            foreach ($rootJoins as $join) {
                if ($join->getJoin() === $alias.'.'.$field) {
                    $joinAlias = $join->getAlias();
                }
            }
        }
        $associationTargetClass = $metadata->getAssociationTargetClass($field);
        $associationTargetMetadata = $this->entityManager->getClassMetadata($associationTargetClass);

        foreach ($associationTargetMetadata->getIdentifier() as $identifier) {
            if ($joinAlias === null) {
                $queryBuilder
                    ->innerJoin($alias.'.'.$field, $field)
                    ->addOrderBy($field.'.'.$identifier, $orderValue)

                ;
            } else {
                $queryBuilder
                    ->addOrderBy($field.'.'.$identifier, $orderValue)
                ;
            }
        }
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
