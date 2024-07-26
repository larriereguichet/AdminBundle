<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventDispatcher;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Add a single entrypoint to dispatch resources and operations generics and specifics events.
 *
 * For instance, if the "my_resource" resource of "my_application" application is created, the following events should
 * be dispatched:
 * - lag_admin.resource.created (generic)
 * - my_application.my_resource.created (specific)
 * - lag_admin.my_resource.created (specific)
 */
interface ResourceEventDispatcherInterface
{
    public function dispatchResourceEvents(
        Event $event,
        string $eventName,
        string $applicationName,
        string $resourceName,
        ?string $operationName = null,
    ): void;
}
