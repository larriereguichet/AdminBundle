<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Event\Dispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Event\Events\ResourceEvent;
use LAG\AdminBundle\Event\ResourceEvents;
use LAG\AdminBundle\Metadata\AdminResource;

class EventResourceFactory implements ResourceFactoryInterface
{
    public function __construct(
        private ResourceEventDispatcherInterface $eventDispatcher,
        private ResourceFactoryInterface $resourceFactory,
    ) {
    }

    public function create(AdminResource $resource): AdminResource
    {
        $event = new ResourceEvent($resource);
        $eventNames = [ResourceEvents::RESOURCE_CREATE, ResourceEvents::NAMED_RESOURCE_CREATE];
        $this->eventDispatcher->dispatchNamedEvents($event, $eventNames, $resource->getName());

        $resource = $this->resourceFactory->create($event->getResource());

        $eventNames = [ResourceEvents::RESOURCE_CREATED, ResourceEvents::NAMED_RESOURCE_CREATED];
        $this->eventDispatcher->dispatchNamedEvents($event, $eventNames, $resource->getName());

        return $resource;
    }
}
