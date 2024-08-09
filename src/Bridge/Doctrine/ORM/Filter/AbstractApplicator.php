<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Filter;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Filter\Applicator\FilterApplicatorInterface;
use LAG\AdminBundle\Resource\Metadata\FilterInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;

abstract readonly class AbstractApplicator implements FilterApplicatorInterface
{
    public function __construct(
        protected Registry $registry,
    ) {
    }

    public function supports(OperationInterface $operation, FilterInterface $filter, mixed $data, mixed $filterValue): bool
    {
        if (!$data instanceof QueryBuilder) {
            return false;
        }

        return $this->registry->getManagerForClass($operation->getResource()->getDataClass()) !== null;
    }
}
