<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventDispatcher;

use LAG\AdminBundle\Event\ResourceEventInterface;

interface ResourceEventDispatcherInterface
{
    /**
     * Dispatch a resource event using the build event dispatcher.
     */
    public function dispatchBuildEvents(ResourceEventInterface $event, string $eventName): void;

    /**
     * Dispatch a resource event using the default event dispatcher.
     */
    public function dispatchEvents(ResourceEventInterface $event, string $eventName): void;
}
