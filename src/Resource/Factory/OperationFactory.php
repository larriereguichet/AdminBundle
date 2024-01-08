<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Filter\Factory\FilterFactoryInterface;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;

readonly class OperationFactory implements OperationFactoryInterface
{
    public function __construct(
        private FilterFactoryInterface $filterFactory,
    ) {
    }

    public function create(OperationInterface $operation): OperationInterface
    {
        if ($operation->getResource() === null) {
            throw new Exception('The operation should be owned by a resource');
        }

        if ($operation instanceof CollectionOperationInterface) {
            $filters = [];

            foreach ($operation->getFilters() ?? [] as $filter) {
                $filters[] = $this->filterFactory->create($filter);
            }
            $operation = $operation->withFilters($filters);
        }

        return $operation;
    }
}
