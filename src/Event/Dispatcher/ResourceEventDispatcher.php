<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Dispatcher;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

use function Symfony\Component\String\u;

class ResourceEventDispatcher implements ResourceEventDispatcherInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function dispatch(
        object $event,
        string $eventName,
        string $resourceName,
        string $operationName,
        string $resourceEventPattern,
        string $operationEventPattern
    ): void {
        $resourceEventName = u($resourceEventPattern)->replace('{resource}', $operationName)->toString();
        $operationEventName = u($operationEventPattern)
            ->replace('{resource}', $resourceName)
            ->replace('{operation}', $operationName)
            ->toString()
        ;

        $this->eventDispatcher->dispatch($event, $eventName);
        $this->eventDispatcher->dispatch($event, $resourceEventName);
        $this->eventDispatcher->dispatch($event, $operationEventName);
    }
}
