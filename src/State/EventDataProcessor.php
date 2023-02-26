<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State;

use LAG\AdminBundle\Event\DataEvents;
use LAG\AdminBundle\Event\Events\DataEvent;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EventDataProcessor implements DataProcessorInterface
{
    public function __construct(
        private DataProcessorInterface $decorated,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $uriVariables = [], array $context = []): void
    {
        $this->eventDispatcher->dispatch(new DataEvent($data), DataEvents::DATA_PROCESS);
        $this->decorated->process($data, $operation, $uriVariables, $context);
        $this->eventDispatcher->dispatch(new DataEvent($data), DataEvents::DATA_PROCESSED);
    }
}
