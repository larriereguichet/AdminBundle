<?php

namespace LAG\AdminBundle\State\Processor;

use LAG\AdminBundle\Event\DataEvent;
use LAG\AdminBundle\Event\DataEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;

final readonly class EventProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private ResourceEventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $uriVariables = [], array $context = []): void
    {
        $this->eventDispatcher->dispatchResourceEvents(
            new DataEvent($data, $operation),
            DataEvents::DATA_PROCESS,
            $operation->getResource()->getApplication(),
            $operation->getResource()->getName(),
            $operation->getName(),
        );

        $this->processor->process($data, $operation, $uriVariables, $context);

        $this->eventDispatcher->dispatchResourceEvents(
            new DataEvent($data, $operation),
            DataEvents::DATA_PROCESSED,
            $operation->getResource()->getApplication(),
            $operation->getResource()->getName(),
            $operation->getName(),
        );
    }
}
