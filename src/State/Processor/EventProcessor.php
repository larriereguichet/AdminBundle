<?php

namespace LAG\AdminBundle\State\Processor;

use LAG\AdminBundle\Event\DataEvent;
use LAG\AdminBundle\Event\DataEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
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
            [DataEvents::DATA_PROCESS, DataEvents::RESOURCE_DATA_PROCESS, DataEvents::OPERATION_DATA_PROCESS],
            $operation->getResource()->getName(),
            $operation->getName(),
        );

        $this->processor->process($data, $operation, $uriVariables, $context);

        $this->eventDispatcher->dispatchNamedEvents(
            new DataEvent($data),
            [DataEvents::DATA_PROCESSED, DataEvents::RESOURCE_DATA_PROCESSED, DataEvents::OPERATION_DATA_PROCESSED],
            $operation->getResource()->getName(),
            $operation->getName(),
        );
    }
}
