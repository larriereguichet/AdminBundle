<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Event\ResourceEvent;
use LAG\AdminBundle\Event\ResourceEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\Metadata\Resource;

/**
 * Decorate the original resource factory to add optional event dispatching.
 */
final readonly class EventResourceFactory implements ResourceFactoryInterface
{
    public function __construct(
        private ResourceEventDispatcherInterface $eventDispatcher,
        private ResourceFactoryInterface $resourceFactory,
    ) {
    }

    public function create(Resource $definition): Resource
    {
        $event = new ResourceEvent($definition);

        $applicationName = $definition->getApplication();
        $resourceName = $definition->getName();

        $this->eventDispatcher->dispatchResourceEvents(
            $event,
            ResourceEvents::RESOURCE_CREATE,
            $applicationName,
            $resourceName,
        );
        $definition = $event->getResource();
        // The operation resource change is not allowed in the event dispatching as it could cause to bypass validation
        $this->assertResourceNameNotChanged($definition, $applicationName, $resourceName);

        $resource = $this->resourceFactory->create($event->getResource());

        $this->eventDispatcher->dispatchResourceEvents(
            new ResourceEvent($resource),
            ResourceEvents::RESOURCE_CREATED,
            $resource->getApplication(),
            $resource->getName()
        );

        return $resource;
    }

    private function assertResourceNameNotChanged(
        Resource $resource,
        string $applicationName,
        string $resourceName
    ): void {
        if ($resource->getApplication() !== $applicationName) {
            throw new Exception(sprintf(
                'Changing the resource "%s" application from "%s" to "%s" is not allowed.',
                $resourceName,
                $applicationName,
                $resource->getApplication(),
            ));
        }

        if ($resource->getName() !== $resourceName) {
            throw new Exception(sprintf(
                'The resource "%s" name change to "%s" is not allowed',
                $resourceName,
                $resource->getName(),
            ));
        }
    }
}
