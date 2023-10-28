<?php

declare(strict_types=1);

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
