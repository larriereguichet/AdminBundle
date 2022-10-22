<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener;

use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataPropertyFactoryInterface;
use LAG\AdminBundle\Event\Events\ResourceCreateEvent;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\OperationInterface;

class ResourceCreateListener
{
    public function __construct(
        private MetadataPropertyFactoryInterface $propertyFactory,
    ) {
    }

    public function __invoke(ResourceCreateEvent $event): void
    {
        $resource = $event->getResource();
        $operations = [];

        foreach ($resource->getOperations() as $operation) {
            $operation = $this->addOperationDefault($resource, $operation);
            $operations[$operation->getName()] = $operation;
        }
        $resource = $resource->withOperations($operations);
        $event->setResource($resource);
    }

    private function addOperationDefault(AdminResource $resource, OperationInterface $operation): OperationInterface
    {
        if (\count($operation->getProperties()) === 0) {
            $operation = $operation->withProperties($this->propertyFactory->createProperties($resource->getDataClass()));
        }

        return $operation;
    }
}
