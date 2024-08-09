<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Filter;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\Metadata\FilterInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\TextFilter;
use function Symfony\Component\String\u;

final readonly class TextFilterApplicator extends AbstractApplicator
{
    public function supports(OperationInterface $operation, FilterInterface $filter, mixed $data, mixed $filterValue): bool
    {
        return parent::supports($operation, $filter, $data, $filterValue) && $filter instanceof TextFilter;
    }

    /**
     * @param TextFilter $filter
     * @param QueryBuilder $data
     */
    public function apply(OperationInterface $operation, FilterInterface $filter, mixed $data, mixed $filterValue): void
    {
        $rootAlias = $data->getRootAliases()[0];
        $this->applyFilter($data, $filter, $filterValue, $rootAlias, $filter->getProperties());


        // TODO move in another filter ?
//        if (u($filter->getPropertyPath())->containsAny('.')) {
//            $this->applyJoinFilter($data, $filter, $filterValue, $rootAlias);
//        }
//
//        if ($metadata->hasField($filter->getPropertyPath())) {
//        }
    }

    private function applyFilter(
        QueryBuilder $queryBuilder,
        FilterInterface $filter,
        mixed $value,
        string $alias,
        array $properties,
    ): void {
        $method = $filter->getOperator() === 'and' ? 'andWhere' : 'orWhere';

        if ($filter->getComparator() === 'between') {
            if (!\is_array($value)) {
                throw new Exception(sprintf(
                    'The parameters for a "between" comparison filter are invalid, expected an array of 2 parameters, got "%s"',
                    is_object($value) ? get_class($value) : gettype($value),
                ));
            }

            if (\count($value) === 2) {
                throw new Exception(sprintf(
                    'The parameters for a "between" comparison filter are invalid, expected 2 parameters, got "%s"',
                    count($value),
                ));
            }
            $parameterName1 = u($filter->getName())
                ->prepend('filter_')
                ->append('_1')
                ->snake()
                ->toString()
            ;
            $parameterName2 = u($filter->getName())
                ->prepend('filter_')
                ->append('_2')
                ->snake()
                ->toString()
            ;
            $wheres = [];

            foreach ($properties as $property) {
                $dql = u('entity.field >= :lower_value and entity.field <= :upper_value')
                    ->replace('entity', $alias)
                    ->replace('field', $property)
                    ->replace('lower_value', $value[0])
                    ->replace('upper_value', $value[1])
                    ->toString()
                ;
                $wheres[] = $dql;
            }
            $queryBuilder->$method($queryBuilder->expr()->orX(...$wheres));
            $queryBuilder->setParameter($parameterName1, $value[0]);
            $queryBuilder->setParameter($parameterName2, $value[1]);

        }

        if ($filter->getComparator() === 'like') {
            $parameterName = u($filter->getName())->prepend('filter_')->snake()->toString();
            $wheres = [];

            foreach ($properties as $property) {
                $dql = u('entity.field like :parameter')
                    ->replace('entity', $alias)
                    ->replace('field', $property)
                    ->replace('parameter', $parameterName)
                    ->toString()
                ;
                $wheres[] = $dql;
            }
            $queryBuilder->$method($queryBuilder->expr()->orX(...$wheres));
            $queryBuilder->setParameter($parameterName, '%'.$value.'%');
        }

        if ($filter->getComparator() === 'equals') {
            $parameterName = u($filter->getName())->prepend('filter_')->snake()->toString();

            foreach ($properties as $property) {
                $dql = u('entity.field = :parameter')
                    ->replace('entity', $alias)
                    ->replace('field', $property)
                    ->replace('parameter', $parameterName)
                    ->toString()
                ;
                $queryBuilder->$method($dql);
            }
            $queryBuilder->setParameter($parameterName, $value);
        }
    }

    private function applyJoinFilter(
        QueryBuilder $queryBuilder,
        FilterInterface $filter,
        mixed $value,
        string $alias
    ): void
    {
        // TODO join filters
        $lastAlias = $alias;
        $joins = u($filter->getPropertyPath())->split('.');
        $field = array_pop($joins);

        foreach ($joins as $join) {
            $alias = $join;
            $queryBuilder->innerJoin($lastAlias.'.'.$join, $alias);
            $lastAlias = $alias;
        }
        $this->applyFilter($queryBuilder, $filter, $value, $alias, $field);
    }
}
