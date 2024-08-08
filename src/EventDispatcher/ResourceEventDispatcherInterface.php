<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventDispatcher;

use Symfony\Contracts\EventDispatcher\Event;

interface ResourceEventDispatcherInterface
{
    public function dispatchResourceEvents(
        Event $event,
        string $eventName,
        string $applicationName,
        string $resourceName,
        string $operationName,
    ): void;

    public function dispatchGridEvents(
        Event $event,
        string $eventName,
        string $applicationName,
        string $resourceName,
        string $gridName,
    ): void;
}
