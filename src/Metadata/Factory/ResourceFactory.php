<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Event\Events\ResourceEvent;
use LAG\AdminBundle\Event\ResourceEvents;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\AdminResource;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ResourceFactory implements ResourceFactoryInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private OperationFactoryInterface $operationFactory,
    ) {
    }

    public function create(AdminResource $definition): AdminResource
    {
        $event = new ResourceEvent($definition);
        $this->eventDispatcher->dispatch($event, ResourceEvents::RESOURCE_CREATE);
        $this->eventDispatcher->dispatch($event, sprintf(ResourceEvents::RESOURCE_CREATE_PATTERN, $definition->getName()));
        $resource = $event->getResource();
        $operations = [];

        foreach ($resource->getOperations() as $operationDefinition) {
            $operations[] = $this->operationFactory->create($resource, $operationDefinition->withResource($resource));
        }
        $resource = $resource->withOperations($operations);
        $event = new ResourceEvent($resource);
        $this->eventDispatcher->dispatch($event, ResourceEvents::RESOURCE_CREATED);
        $this->eventDispatcher->dispatch($event, sprintf(ResourceEvents::RESOURCE_CREATED_PATTERN, $resource->getName()));

        if ($event->getResource()->getName() !== $definition->getName()) {
            throw new Exception(sprintf('The resource name "%s" to "%s" change is not allowed', $definition->getName(), $resource->getName()));
        }

        return $event->getResource();
    }
}
