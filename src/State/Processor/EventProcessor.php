<?php

declare(strict_types=1);

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

    public function process(mixed $data, OperationInterface $operation, array $urlVariables = [], array $context = []): void
    {
        $this->eventDispatcher->dispatchEvents(
            new DataEvent($data, $operation),
            DataEvents::DATA_PROCESS_EVENT_PATTERN,
            $operation->getResource()->getApplication(),
            $operation->getResource()->getName(),
            $operation->getName(),
        );

        $this->processor->process($data, $operation, $urlVariables, $context);

        $this->eventDispatcher->dispatchEvents(
            new DataEvent($data, $operation),
            DataEvents::DATA_PROCESSED_EVENT_PATTERN,
            $operation->getResource()->getApplication(),
            $operation->getResource()->getName(),
            $operation->getName(),
        );
    }
}
