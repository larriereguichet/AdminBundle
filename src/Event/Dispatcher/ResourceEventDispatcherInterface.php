<?php

namespace LAG\AdminBundle\Event\Dispatcher;

interface ResourceEventDispatcherInterface
{
    public function dispatch(
        object $event,
        string $eventName,
        string $resourceName,
        string $operationName,
        string $resourceEventPattern,
        string $operationEventPattern
    ): void;
}
