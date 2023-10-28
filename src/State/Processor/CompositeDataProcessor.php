<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Processor;

use LAG\AdminBundle\Event\DataEvents;
use LAG\AdminBundle\Event\Dispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Event\Events\DataEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\OperationInterface;

class CompositeDataProcessor implements DataProcessorInterface
{
    public function __construct(
        private ResourceEventDispatcherInterface $eventDispatcher,
        /** @var DataProcessorInterface[] $processors */
        private iterable $processors = [],
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $uriVariables = [], array $context = []): void
    {
        /** @var DataProcessorInterface $processor */
        foreach ($this->processors as $processor) {
            if ($processor::class === $operation->getProcessor()) {
                $this->dispatchPreEvent($data, $operation);
                $processor->process($data, $operation, $uriVariables, $context);
                $this->dispatchPostEvent($data, $operation);

                return;
            }
        }

        throw new Exception(sprintf('The resource "%s" and operation "%s" is not supported by any processor', $operation->getResource()->getName(), $operation->getName()));
    }

    private function dispatchPreEvent(mixed $data, OperationInterface $operation): void
    {
        $this->eventDispatcher->dispatch(
            new DataEvent($data),
            DataEvents::DATA_PROCESS,
            DataEvents::DATA_RESOURCE_PROCESS,
            DataEvents::DATA_OPERATION_PROCESS,
            $operation->getResource()->getName(),
            $operation->getName(),
        );
    }

    private function dispatchPostEvent(mixed $data, OperationInterface $operation): void
    {
        $this->eventDispatcher->dispatch(
            new DataEvent($data),
            DataEvents::DATA_PROCESSED,
            DataEvents::DATA_RESOURCE_PROCESSED,
            DataEvents::DATA_OPERATION_PROCESSED,
            $operation->getResource()->getName(),
            $operation->getName(),
        );
    }
}
