<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventDispatcher;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Contracts\EventDispatcher\Event;

interface ResourceEventDispatcherInterface
{
    public function dispatchBuildEvents(
        Event $event,
        string $eventPattern,
        string $applicationName,
        string $resourceName,
        ?string $operationName = null,
    ): void;

    public function dispatchEvents(
        Event $event,
        string $eventPattern,
        string $applicationName,
        string $resourceName,
        ?string $operationName = null,
        ?string $gridName = null,
    ): void;

    public function dispatchOperationEvents(
        Event $event,
        string $eventPattern,
        OperationInterface $operation,
    ): void;
}
