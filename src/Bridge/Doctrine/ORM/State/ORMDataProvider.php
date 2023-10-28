<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Exception\EntityManagerNotFoundException;
use LAG\AdminBundle\Bridge\Doctrine\ORM\QueryBuilder\QueryBuilderHelper;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\DataProviderInterface;
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
        // Add a suffix to avoid error if the resource is named with a reserved keyword (like group)
        $rootAlias = $operation->getResource()->getName().'_entity';

        $queryBuilder = $repository->createQueryBuilder($rootAlias);
        $classMetadata = $manager->getClassMetadata($operation->getResource()->getDataClass());
        $helper = new QueryBuilderHelper($queryBuilder, $classMetadata);

        if ($operation instanceof CollectionOperationInterface) {
            $orderBy = $operation->getOrderBy();

            if (($context['sort'] ?? false) && ($context['order'] ?? false)) {
                $orderBy = [$context['sort'] => $context['order']];
            }
            $helper->addOrderBy($orderBy);
            $filters = [];

            foreach ($operation->getFilters() ?? [] as $filter) {
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
            $pager = new Pagerfanta(new QueryAdapter($helper->getQueryBuilder(), true, true));
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
}
