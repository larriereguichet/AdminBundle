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

    public function dispatchNamedEvents(
        Event $event,
        string $eventName,
        string $applicationName,
        string $resourceName,
        ?string $operationName = null,
    ): void {
        $eventName = u($eventName);
        $eventNames = [
            $eventName,
            $eventName->replace('resource', '{application}'),
            $eventName->replace('resource', '{application}.{resource}'),
        ];

        // As the application and resource names are mandatory, the operation can be optional for build resource events
        // for instance
        if ($operationName) {
            $eventNames[] = $eventName->replace('resource', '{application}.{resource}.{operation}');
        }

        foreach ($eventNames as $eventNameString) {
            $eventName = $eventNameString
                ->replace('{application}', $resourceName)
                ->replace('{resource}', $resourceName)
                ->replace('{operation}', $operationName ?? '')
                ->toString()
            ;
            $this->eventDispatcher->dispatch($event, $eventName);
        }
    }
}
