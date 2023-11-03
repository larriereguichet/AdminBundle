<?php

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Event\Dispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Event\Events\OperationEvent;
use LAG\AdminBundle\Event\OperationEvents;
use LAG\AdminBundle\Metadata\OperationInterface;

class EventOperationFactory implements OperationFactoryInterface
{
    public function __construct(
        private ResourceEventDispatcherInterface $eventDispatcher,
        private OperationFactoryInterface $operationFactory,
    )
    {
    }

    public function create(OperationInterface $operation): OperationInterface
    {
        $event = new OperationEvent($operation);
        $eventNames = [
            OperationEvents::OPERATION_CREATE,
            OperationEvents::NAMED_RESOURCE_OPERATION_CREATE,
            OperationEvents::NAMED_RESOURCE_OPERATION_CREATE,
        ];
        $this->eventDispatcher->dispatchNamedEvents($event, $eventNames, $operation->getResource()->getName());

        $operation = $this->operationFactory->create($event->getOperation());

        $eventNames = [
            OperationEvents::OPERATION_CREATED,
            OperationEvents::NAMED_RESOURCE_OPERATION_CREATED,
            OperationEvents::NAMED_RESOURCE_OPERATION_CREATED,
        ];
        $this->eventDispatcher->dispatchNamedEvents($event, $eventNames, $operation->getName());

        return $operation;
    }
}
