<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventDispatcher;

use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use function Symfony\Component\String\u;

readonly class ResourceEventDispatcher implements ResourceEventDispatcherInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function dispatchResourceEvents(
        Event $event,
        string $eventName,
        string $applicationName,
        string $resourceName,
        ?string $operationName = null,
    ): void {
        $eventName = u($eventName);
        $eventNames = [
            $eventName->prepend('lag_admin.'),
            $eventName->replace('resource', '{application}.{resource}'),
        ];

        // As the application and resource names are mandatory, the operation can be optional for build resource events
        // for instance
        if ($operationName) {
            $eventNames = [
                $eventName->prepend('lag_admin.'),
                $eventName->replace('operation', '{application}.{resource}.{operation}')
            ];
        }

        foreach ($eventNames as $eventNameString) {
            $eventName = $eventNameString
                ->replace('{application}', $applicationName)
                ->replace('{resource}', $resourceName)
                ->replace('{operation}', $operationName ?? '')
                ->toString()
            ;
            $this->eventDispatcher->dispatch($event, $eventName);
        }
    }

    public function dispatchGridEvents(
        Event $event,
        string $eventName,
        string $applicationName,
        string $resourceName,
        string $gridName,
    ): void {
        $eventName = u($eventName);
        $eventNames = [

        ];
    }
}
