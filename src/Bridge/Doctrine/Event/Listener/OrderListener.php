<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\Event\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Bridge\Doctrine\DataSource\ORMDataSource;
use LAG\AdminBundle\Event\Events\DataOrderEvent;

class OrderListener
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
}
