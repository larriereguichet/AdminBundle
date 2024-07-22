<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\QueryBuilder;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\Mapping\ClassMetadata;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\UnexpectedTypeException;
use LAG\AdminBundle\Filter\FilterInterface;
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
