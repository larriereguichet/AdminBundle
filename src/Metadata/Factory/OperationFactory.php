<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Event\Events\OperationEvent;
use LAG\AdminBundle\Event\OperationEvents;
use LAG\AdminBundle\Filter\Factory\FilterFactoryInterface;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OperationFactory implements OperationFactoryInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private PropertyFactoryInterface $propertyFactory,
        private FilterFactoryInterface $filterFactory,
    ) {
    }

    public function create(AdminResource $resource, OperationInterface $operationDefinition): OperationInterface
    {
        $operationDefinition = $operationDefinition
            ->withResource($resource)
        ;
        $event = new OperationEvent($operationDefinition);
        $this->eventDispatcher->dispatch($event, OperationEvents::OPERATION_CREATE);
        $this->eventDispatcher->dispatch($event, sprintf(
            OperationEvents::OPERATION_CREATE_PATTERN,
            $resource->getName(),
            $operationDefinition->getName(),
        ));
        $properties = [];
        $operation = $event->getOperation();

        foreach ($operation->getProperties() as $property) {
            $properties[] = $this->propertyFactory->create($property);
        }
        $operation = $operation->withProperties($properties);

        if ($operation instanceof CollectionOperationInterface) {
            $filters = [];

            foreach ($operation->getFilters() ?? [] as $filter) {
                $filters[] = $this->filterFactory->create($filter);
            }
            $operation = $operation->withFilters($filters);
        }

        $event = new OperationEvent($operation);
        $this->eventDispatcher->dispatch($event, OperationEvents::OPERATION_CREATED);
        $this->eventDispatcher->dispatch($event, sprintf(
            OperationEvents::OPERATION_CREATED_PATTERN,
            $resource->getName(),
            $operation->getName(),
        ));

        // Ensure the operation belongs to the right resource
        return $event
            ->getOperation()
            ->withResource($resource)
        ;
    }
}
