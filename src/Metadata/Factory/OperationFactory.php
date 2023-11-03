<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Event\Events\OperationEvent;
use LAG\AdminBundle\Event\OperationEvents;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Filter\Factory\FilterFactoryInterface;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OperationFactory implements OperationFactoryInterface
{
    public function __construct(
        private PropertyFactoryInterface $propertyFactory,
        private FilterFactoryInterface $filterFactory,
    ) {
    }

    public function create(OperationInterface $operation): OperationInterface
    {
        if ($operation->getResource() === null) {
            throw new Exception('The operation should be owned by a resource');
        }
        $operation = $operation->withProperties($this->propertyFactory->createCollection($operation));

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
