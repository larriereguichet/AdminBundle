<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Filter;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Attribute\TextFilter;
use LAG\AdminBundle\Metadata\FilterInterface;
use LAG\AdminBundle\Metadata\OperationInterface;

use function Symfony\Component\String\u;

final readonly class TextFilterApplicator extends AbstractApplicator
{
    /**
     * @param TextFilter $filter
     * @param QueryBuilder $data
     */
    public function apply(OperationInterface $operation, FilterInterface $filter, mixed $data, mixed $filterValue): void
    {
        if ($filter->getProperties() === null) {
            return;
        }

        $rootAlias = $data->getRootAliases()[0];
        $grouped = [];

        foreach ($filter->getProperties() as $property) {
            if (!str_contains($property, '.')) {
                $grouped[$rootAlias][] = $property;
            } else {
                [$joinAlias, $field] = $this->resolveJoin($data, $rootAlias, $property);
                $grouped[$joinAlias][] = $field;
            }
        }

        foreach ($grouped as $alias => $fields) {
            $this->applyFilter($data, $filter, $filterValue, $alias, $fields);
        }
    }

    protected function supportsFilter(FilterInterface $filter): bool
    {
        return $filter instanceof TextFilter;
    }

    /** @param string[] $properties */
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
                throw new Exception(\sprintf('The parameters for a "between" comparison filter are invalid, expected an array of 2 parameters, got "%s"', get_debug_type($value)));
            }

            if (\count($value) === 2) {
                throw new Exception(\sprintf('The parameters for a "between" comparison filter are invalid, expected 2 parameters, got "%s"', \count($value)));
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
}
