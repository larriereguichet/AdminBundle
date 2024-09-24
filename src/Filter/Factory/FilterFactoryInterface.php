<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Filter\Factory;

use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\FilterInterface;

interface FilterFactoryInterface
{
    /**
     * Create a filter for the given operation to allow filtering a collection of data.
     */
    public function create(CollectionOperationInterface $operation, FilterInterface $filter): FilterInterface;
}
