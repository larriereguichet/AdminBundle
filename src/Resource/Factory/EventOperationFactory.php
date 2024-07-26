<?php

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Event\OperationEvent;
use LAG\AdminBundle\Event\OperationEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;

final readonly class EventOperationFactory implements OperationFactoryInterface
{
    public function __construct(
        private ResourceEventDispatcherInterface $eventDispatcher,
        private OperationFactoryInterface $operationFactory,
    ) {
    }

    public function create(OperationInterface $operation): OperationInterface
    {
        $event = new OperationEvent($operation);
        $this->eventDispatcher->dispatchResourceEvents(
            $event,
            OperationEvents::OPERATION_CREATE,
            $operation->getResource()->getApplication(),
            $operation->getResource()->getName(),
            $operation->getName(),
        );

        $operation = $this->operationFactory->create($event->getOperation());

        $this->eventDispatcher->dispatchResourceEvents(
            $event,
            OperationEvents::OPERATION_CREATED,
            $operation->getResource()->getApplication(),
            $operation->getResource()->getName(),
            $operation->getName()
        );

        return $operation;
    }
}
