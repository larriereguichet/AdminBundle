<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\Event\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Bridge\Doctrine\DataSource\ORMDataSource;
use LAG\AdminBundle\Event\Events\DataFilterEvent;
use LAG\AdminBundle\Exception\UnexpectedTypeException;
use LAG\AdminBundle\Filter\FilterInterface;
use function Symfony\Component\String\u;

class FilterListener
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(DataFilterEvent $event): void
    {
        $dataSource = $event->getDataSource();

        if (!$dataSource instanceof ORMDataSource) {
            return;
        }
        $filters = $event->getFilters();
        $queryBuilder = $dataSource->getData();
        $metadata = $this->entityManager->getClassMetadata($event->getAdmin()->getEntityClass());

        foreach ($filters as $filter) {
            if (!$filter instanceof FilterInterface) {
                throw new UnexpectedTypeException($filter, FilterInterface::class);
            }

            if ($metadata->hasField($filter->getName())) {
                $this->addFieldFilter($queryBuilder, $filter);
            }

            if ($metadata->hasAssociation($filter->getName())) {
                $this->addAssociationFilter($queryBuilder, $filter);
            }
        }
    }

    private function addFieldFilter(QueryBuilder $queryBuilder, FilterInterface $filter): void
    {
        $alias = $queryBuilder->getRootAliases()[0];
        $value = $filter->getValue();
        $method = $filter->getOperator() === 'and' ? 'andWhere' : 'orWhere';

        if ('between' === $filter->getComparator()) {
            $parameterName1 = 'filter_'.u($filter->getName())->snake()->toString().'_1';
            $parameterName2 = 'filter_'.u($filter->getName())->snake()->toString().'_2';
            $dql = sprintf(
                '%s.%s > :%s and %s.%s < :%s',
                $alias,
                $filter->getName(),
                $parameterName1,
                $alias,
                $filter->getName(),
                $parameterName2
            );
            $queryBuilder->$method($dql);
            $queryBuilder->setParameter($parameterName1, array_shift($value));
            $queryBuilder->setParameter($parameterName2, array_shift($value));

            return;
        }

        if ($filter->getComparator() === 'like') {
            $value = '%'.$value.'%';
        }
        $parameterName = 'filter_'.u($filter->getName())->snake()->toString();

        $dql = sprintf(
            '%s.%s %s :%s',
            $alias,
            $filter->getName(),
            $filter->getComparator(),
            $parameterName
        );
        $queryBuilder->$method($dql);
        $queryBuilder->setParameter($parameterName, $value);
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
}
