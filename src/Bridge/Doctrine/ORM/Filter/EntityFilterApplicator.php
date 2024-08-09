<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Filter;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Resource\Metadata\EntityFilter;
use LAG\AdminBundle\Resource\Metadata\FilterInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;

final readonly class EntityFilterApplicator extends AbstractApplicator
{
    public function supports(OperationInterface $operation, FilterInterface $filter, mixed $data, mixed $filterValue): bool
    {
        return parent::supports($operation, $filter, $data, $filterValue) && $filter instanceof EntityFilter;
    }


    /**
     * @param EntityFilter $filter
     * @param QueryBuilder $data
     */
    public function apply(OperationInterface $operation, FilterInterface $filter, mixed $data, mixed $filterValue): void
    {
        $rootAlias = $data->getRootAliases()[0];
        $method = $filter->getOperator() === 'and' ? 'andWhere' : 'orWhere';

        $data->innerJoin($rootAlias.'.'.$filter->getProperty(), $filter->getName().'_alias');
        $data->$method($filter->getName().'_alias = :'.$filter->getName().'_value');
        $data->setParameter($filter->getName().'_value', $filterValue);
    }
}
