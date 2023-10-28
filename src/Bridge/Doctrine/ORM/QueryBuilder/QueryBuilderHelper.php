<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\QueryBuilder;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\Mapping\ClassMetadata;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\UnexpectedTypeException;
use LAG\AdminBundle\Metadata\Filter\FilterInterface;

use function Symfony\Component\String\u;

class QueryBuilderHelper
{
    private string $rootAlias;

    public function __construct(
        private QueryBuilder $queryBuilder,
        private ClassMetadata $metadata,
    ) {
        $this->rootAlias = $this->queryBuilder->getRootAliases()[0];
    }

    public function addOrderBy(array $orderBy): self
    {
        foreach ($orderBy as $propertyPath => $order) {
            $propertyPath = u($propertyPath);

            if ($propertyPath->containsAny('.')) {
                $joinRootAlias = $this->rootAlias;

                foreach ($propertyPath->split('.') as $path) {
                    $this->leftJoin($path->toString(), $joinRootAlias);
                }
            } else {
                if ($this->metadata->hasField($propertyPath->toString())) {
                    $this->queryBuilder->addOrderBy(
                        $propertyPath->prepend($this->rootAlias.'.')->toString(),
                        $order
                    );
                }

                if ($this->metadata->isSingleValuedAssociation($propertyPath->toString())) {
                    $joinProperty = $propertyPath->prepend('.')->prepend($this->rootAlias)->toString();

                    if (!$this->hasJoin($propertyPath->toString())) {
                        $this->queryBuilder->innerJoin($joinProperty, $propertyPath->toString());
                    }
                    // TODO wip order by join
                }
            }
        }

        return $this;
    }

    public function addFilters(array $filters): self
    {
        foreach ($filters as $filter) {
            if (!$filter instanceof FilterInterface) {
                throw new UnexpectedTypeException($filter, FilterInterface::class);
            }
            $data = $filter->getData();
            $propertyPath = u($filter->getPropertyPath());

            // Do not filter on null values
            if ($data === null) {
                continue;
            }

            if ($this->metadata->hasField($propertyPath->toString())) {
                $method = $filter->getOperator() === 'and' ? 'andWhere' : 'orWhere';

                if ($filter->getComparator() === 'between') {
                    if (!\is_array($data) || \count($data) === 2) {
                        throw new Exception('Parameters for a between comparison filter are invalid');
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

                    $dql = sprintf(
                        'entity.%s > :%s and entity.%s < :%s',
                        $filter->getPropertyPath(),
                        $parameterName1,
                        $filter->getPropertyPath(),
                        $parameterName2
                    );
                    $this->queryBuilder->$method($dql);
                    $this->queryBuilder->setParameter($parameterName1, $filter->getData()[0]);
                    $this->queryBuilder->setParameter($parameterName2, $filter->getData()[1]);

                    continue;
                } elseif ($filter->getComparator() === 'like') {
                    if (\is_string($data)) {
                        $data = '%'.$data.'%';
                    } else {
                        $data = '%'.$data->__toString().'%';
                    }
                }
                $parameterName = u($filter->getName())->prepend('filter_')->snake()->toString();

                $dql = sprintf(
                    '%s.%s %s :%s',
                    $this->rootAlias,
                    $filter->getName(),
                    $filter->getComparator(),
                    $parameterName
                );
                $this->queryBuilder->$method($dql);
                $this->queryBuilder->setParameter($parameterName, $data);
            }
        }

        return $this;
    }

    public function leftJoin(string $joinAlias, string $rootAlias): self
    {
        if (!$this->hasJoin($joinAlias)) {
            $dql = u($rootAlias)
                ->append('.')
                ->append($joinAlias)
                ->toString()
            ;
            $this->queryBuilder->leftJoin($dql, u($joinAlias)->append('_resource')->toString());
        }

        return $this;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    private function hasJoin(string $joinAlias): bool
    {
        $dqlPart = $this->queryBuilder->getDQLPart('join');

        foreach ($dqlPart as $joins) {
            /** @var Join $join */
            foreach ($joins as $join) {
                if ($join->getAlias() === $joinAlias.'_resource') {
                    return true;
                }
            }
        }

        return false;
    }
}
