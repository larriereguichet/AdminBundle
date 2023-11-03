<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Dispatcher;

use Symfony\Contracts\EventDispatcher\Event;

interface ResourceEventDispatcherInterface
{
    public function dispatch(
        Event $event,
        string $eventName,
        string $resourceName,
        string $operationName,
        string $resourceEventPattern,
        string $operationEventPattern
    ): void;

    public function dispatchNamedEvents(
        Event $event,
        array $eventNames,
        string $resourceName,
        ?string $operationName = null
    ): void;
}
