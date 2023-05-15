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
        $operation = $this->dispatchPreEvents($resource, $operationDefinition);
        $operation = $operation->withProperties($this->propertyFactory->createCollection($operation));

        if ($operation instanceof CollectionOperationInterface) {
            $filters = [];

            foreach ($operation->getFilters() ?? [] as $filter) {
                $filters[] = $this->filterFactory->create($filter);
            }
            $operation = $operation->withFilters($filters);
        }

        $operation = $this->dispatchPostEvents($resource, $operation);

        // Ensure the operation belongs to the right resource
        return $operation->withResource($resource);
    }

    private function dispatchPreEvents(AdminResource $resource, OperationInterface $operationDefinition): OperationInterface
    {
        $event = new OperationEvent($operationDefinition);
        $this->eventDispatcher->dispatch($event, OperationEvents::OPERATION_CREATE);
        $this->eventDispatcher->dispatch($event, sprintf(
            OperationEvents::OPERATION_CREATE_PATTERN,
            $resource->getName(),
        ));
        $this->eventDispatcher->dispatch($event, sprintf(
            OperationEvents::OPERATION_CREATE_RESOURCE_PATTERN,
            $resource->getName(),
            $operationDefinition->getName(),
        ));

        return $event->getOperation();
    }

    private function dispatchPostEvents(AdminResource $resource, OperationInterface $operation): OperationInterface
    {
        $event = new OperationEvent($operation);
        $this->eventDispatcher->dispatch($event, OperationEvents::OPERATION_CREATED);
        $this->eventDispatcher->dispatch($event, sprintf(
            OperationEvents::OPERATION_CREATED_PATTERN,
            $resource->getName(),
        ));
        $this->eventDispatcher->dispatch($event, sprintf(
            OperationEvents::OPERATION_CREATED_RESOURCE_PATTERN,
            $resource->getName(),
            $operation->getName(),
        ));

        return $event->getOperation();
    }
}
