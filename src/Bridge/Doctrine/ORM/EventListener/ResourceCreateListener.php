<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener;

use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataPropertyFactoryInterface;
use LAG\AdminBundle\Event\AdminEvent;
use LAG\AdminBundle\Metadata\Admin;
use LAG\AdminBundle\Metadata\OperationInterface;

class ResourceCreateListener
{
    public function __construct(
        private MetadataPropertyFactoryInterface $propertyProvider,
    ) {
    }

    public function __invoke(AdminEvent $event): void
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

    private function addOperationDefault(Admin $resource, OperationInterface $operation): OperationInterface
    {
        if (count($operation->getProperties()) === 0) {
            $operation = $operation->withProperties($this->propertyProvider->createProperties($resource->getDataClass()));
        }

        return $operation;
    }
}
