<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Event\OperationEvent;
use LAG\AdminBundle\Event\OperationEvents;
use LAG\AdminBundle\Event\ResourceEvent;
use LAG\AdminBundle\Event\ResourceEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Metadata\Resource;

final readonly class ResourceFactory implements ResourceFactoryInterface
{
    public function __construct(
        private DefinitionFactoryInterface $definitionFactory,
        private ResourceEventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function create(string $resourceName): Resource
    {
        $definition = $this->definitionFactory->createResourceDefinition($resourceName);
        $applicationName = $definition->getApplication();
        $resourceName = $definition->getName();

        $event = new ResourceEvent($definition);
        $this->eventDispatcher->dispatchBuildEvents($event, ResourceEvents::RESOURCE_CREATE_TEMPLATE);

        $resource = $event->getResource()
            ->withApplication($applicationName)
            ->withName($resourceName)
        ;

        foreach ($resource->getOperations() as $operation) {
            $operationEvent = new OperationEvent($operation->withResource($resource));
            $this->eventDispatcher->dispatchBuildEvents($operationEvent, OperationEvents::OPERATION_CREATE_TEMPLATE);

            $operation = $operationEvent->getOperation();
            $resource = $resource->withOperation($operation);

            $operationEvent = new OperationEvent($operation->withResource($resource));
            $this->eventDispatcher->dispatchBuildEvents($operationEvent, OperationEvents::OPERATION_CREATED_TEMPLATE);
        }
        $event = new ResourceEvent($resource);
        $this->eventDispatcher->dispatchBuildEvents($event, ResourceEvents::RESOURCE_CREATED_TEMPLATE);

        return $resource;
    }
}
