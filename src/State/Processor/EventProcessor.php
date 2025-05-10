<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Processor;

use LAG\AdminBundle\Event\DataEvent;
use LAG\AdminBundle\Event\DataEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Metadata\OperationInterface;

final readonly class EventProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private ResourceEventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $urlVariables = [], array $context = []): void
    {
        $this->eventDispatcher->dispatchBuildEvents(
            new DataEvent($data, $operation),
            DataEvents::DATA_PROCESS_EVENT_TEMPLATE,
        );

        $this->processor->process($data, $operation, $urlVariables, $context);

        $this->eventDispatcher->dispatchBuildEvents(
            new DataEvent($data, $operation),
            DataEvents::DATA_PROCESSED_EVENT_TEMPLATE,
        );
    }
}
