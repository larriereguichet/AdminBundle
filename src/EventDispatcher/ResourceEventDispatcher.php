<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventDispatcher;

use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

use function Symfony\Component\String\u;

final readonly class ResourceEventDispatcher implements ResourceEventDispatcherInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private EventDispatcherInterface $buildEventDispatcher,
    ) {
    }

    public function dispatchBuildEvents(
        Event $event,
        string $eventPattern,
        string $applicationName,
        string $resourceName,
        ?string $operationName = null
    ): void {
        $eventNames = $this->buildEvents(
            $eventPattern,
            $applicationName,
            $resourceName,
            $operationName,
        );

        foreach ($eventNames as $eventName) {
            $this->buildEventDispatcher->dispatch($event, $eventName);
        }
    }

    public function dispatchEvents(
        Event $event,
        string $eventPattern,
        string $applicationName,
        string $resourceName,
        ?string $operationName = null,
        ?string $gridName = null,
    ): void {
        $eventNames = $this->buildEvents(
            $eventPattern,
            $applicationName,
            $resourceName,
            $operationName,
        );

        foreach ($eventNames as $eventName) {
            $this->eventDispatcher->dispatch($event, $eventName);
        }
    }

    private function buildEvents(
        string $eventPattern,
        string $applicationName,
        string $resourceName,
        ?string $operationName = null,
        ?string $gridName = null,
    ): array {
        $eventPattern = u($eventPattern);
        $eventNames = [
            // Generic event
            $eventPattern
                ->replace('{application}', 'lag_admin')
                ->replace('{resource}', 'resource')
                ->toString(),

            // Application event
            $eventPattern
                ->replace('{application}', $applicationName)
                ->replace('{resource}', 'resource')
                ->toString(),

            // Resource event
            $eventPattern
                ->replace('{application}', $applicationName)
                ->replace('{resource}', $resourceName)
                ->toString(),
        ];

        if ($operationName !== null) {
            // Operation event
            $eventNames[] = $eventPattern
                ->replace('{application}', $applicationName)
                ->replace('{resource}', $resourceName.'.'.$operationName)
                ->toString()
            ;
        }

        if ($gridName !== null) {
            $eventNames[] = $eventPattern
                ->replace('{application}', $applicationName)
                ->replace('{resource}', $resourceName.'.'.$operationName)
                ->replace('{grid}', $gridName)
                ->toString()
            ;
        }

        return $eventNames;
    }
}
