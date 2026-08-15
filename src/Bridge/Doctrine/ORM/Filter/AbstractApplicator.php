<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Filter;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Filter\Applicator\FilterApplicatorInterface;
use LAG\AdminBundle\Metadata\FilterInterface;
use LAG\AdminBundle\Metadata\OperationInterface;

abstract readonly class AbstractApplicator implements FilterApplicatorInterface
{
    abstract protected function supportsFilter(FilterInterface $filter): bool;

    public function __construct(
        protected Registry $registry,
    ) {
    }

    public function supports(OperationInterface $operation, FilterInterface $filter, mixed $data, mixed $filterValue): bool
    {
        if (!$this->supportsFilter($filter)) {
            return false;
        }

        if (!$data instanceof QueryBuilder) {
            return false;
        }

        return $this->registry->getManagerForClass($operation->getResource()->getResourceClass()) !== null;
    }

    /**
     * Resolves a dotted property path (e.g. 'author.publisher.name') into [joinAlias, fieldName],
     * adding LEFT JOINs to the QueryBuilder as needed.
     *
     * @return array{string, string}
     */
    protected function resolveJoin(QueryBuilder $qb, string $rootAlias, string $propertyPath): array
    {
        $parts = explode('.', $propertyPath);
        $field = array_pop($parts);
        $currentAlias = $rootAlias;
        $pathSegments = [];

        foreach ($parts as $relation) {
            $pathSegments[] = $relation;
            $joinAlias = 'lag_filter_'.implode('_', $pathSegments);

            // Every filter of the operation is applied to the same query builder, so another filter may have
            // already joined that relation: Doctrine rejects a duplicated alias
            if (!\in_array($joinAlias, $qb->getAllAliases(), true)) {
                $qb->leftJoin($currentAlias.'.'.$relation, $joinAlias);
            }
            $currentAlias = $joinAlias;
        }

        return [$currentAlias, $field];
    }
}
