<?php

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Event\OperationEvent;
use LAG\AdminBundle\Event\OperationEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Metadata\OperationInterface;

readonly class EventOperationFactory implements OperationFactoryInterface
{
    public function __construct(
        private ResourceEventDispatcherInterface $eventDispatcher,
        private OperationFactoryInterface $operationFactory,
    ) {
    }

    public function create(OperationInterface $operation): OperationInterface
    {
        $event = new OperationEvent($operation);
        $this->eventDispatcher->dispatchNamedEvents(
            $event,
            OperationEvents::OPERATION_CREATE,
            $operation->getResource()->getApplicationName(),
            $operation->getResource()->getName(),
            $operation->getName(),
        );

        $operation = $this->operationFactory->create($event->getOperation());

        $this->eventDispatcher->dispatchNamedEvents(
            $event,
            OperationEvents::OPERATION_CREATED,
            $operation->getResource()->getApplicationName(),
            $operation->getResource()->getName(),
            $operation->getName()
        );

        return $operation;
    }
}
