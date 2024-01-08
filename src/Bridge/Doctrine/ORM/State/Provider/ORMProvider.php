<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Exception\ManagerNotFoundException;
use LAG\AdminBundle\Bridge\Doctrine\ORM\QueryBuilder\QueryBuilderHelper;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class ORMProvider implements ProviderInterface
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
            throw new ManagerNotFoundException($operation);
        }
        /** @var EntityRepository $repository */
        $repository = $manager->getRepository($operation->getResource()->getDataClass());
        // Add a suffix to avoid error if the resource is named with a reserved keyword (like group)
        $rootAlias = $operation->getResource()->getName().'_entity';

        $queryBuilder = $repository->createQueryBuilder($rootAlias);
        $classMetadata = $manager->getClassMetadata($operation->getResource()->getDataClass());

        if ($operation instanceof CollectionOperationInterface) {
            $helper = new QueryBuilderHelper($queryBuilder, $classMetadata);
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

            return $helper->getQueryBuilder();
        }
        $queryBuilder = $repository->createQueryBuilder('entity');
        $index = 0;

        foreach ($operation->getIdentifiers() as $identifier) {
            if ($uriVariables[$identifier] ?? false) {
                $parameterName = 'identifier_'.$index;
                $queryBuilder
                    ->andWhere(sprintf('entity.%s = :%s', $identifier, $parameterName))
                    ->setParameter($parameterName, $uriVariables[$identifier])
                ;
                $index++;
            }
        }

        return $queryBuilder;
    }
}
