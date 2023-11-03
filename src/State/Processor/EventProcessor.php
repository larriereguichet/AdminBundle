<?php

namespace LAG\AdminBundle\State\Processor;

use LAG\AdminBundle\Event\DataEvents;
use LAG\AdminBundle\Event\Dispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Event\Events\DataEvent;
use LAG\AdminBundle\Metadata\OperationInterface;

class EventProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private ResourceEventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $uriVariables = [], array $context = []): void
    {
        $this->eventDispatcher->dispatchNamedEvents(
            new DataEvent($data),
            [DataEvents::DATA_PROCESS, DataEvents::DATA_RESOURCE_PROCESS, DataEvents::DATA_OPERATION_PROCESS],
            $operation->getResource()->getName(),
            $operation->getName(),
        );

        $this->processor->process($data, $operation, $uriVariables, $context);

        $this->eventDispatcher->dispatchNamedEvents(
            new DataEvent($data),
            [DataEvents::DATA_PROCESSED, DataEvents::DATA_RESOURCE_PROCESSED, DataEvents::DATA_OPERATION_PROCESSED],
            $operation->getResource()->getName(),
            $operation->getName(),
        );
    }
}
