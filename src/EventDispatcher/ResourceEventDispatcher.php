<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventDispatcher;

use LAG\AdminBundle\Event\ResourceEventInterface;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use function Symfony\Component\String\u;

final readonly class ResourceEventDispatcher implements ResourceEventDispatcherInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function dispatchEvents(ResourceEventInterface $event, string $eventName): void
    {
        $resource = $event->getResource();
        $eventNames = $this->getEventNames($eventName, $resource);

        foreach ($eventNames as $currentEventName) {
            $this->eventDispatcher->dispatch($event, $currentEventName);
        }
    }

    private function getEventNames(string $eventName, Resource $resource): iterable
    {
        $template = u($eventName);

        return [
            $template
                ->replace('{application}', 'lag_admin')
                ->replace('{resource}', 'resource')
                ->toString(),
            $template
                ->replace('{application}', $resource->getApplication())
                ->replace('{resource}', 'resource')
                ->toString(),
            $template
                ->replace('{application}', $resource->getApplication())
                ->replace('{resource}', $resource->getName())
                ->toString(),
        ];
    }
}
