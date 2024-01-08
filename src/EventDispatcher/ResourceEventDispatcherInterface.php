<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventDispatcher;

use Symfony\Contracts\EventDispatcher\Event;

interface ResourceEventDispatcherInterface
{
    public function dispatchNamedEvents(
        Event $event,
        string $eventName,
        string $applicationName,
        string $resourceName,
        string $operationName,
    ): void;
}
