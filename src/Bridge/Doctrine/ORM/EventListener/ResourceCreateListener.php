<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener;

use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataPropertyFactoryInterface;
use LAG\AdminBundle\Event\Events\ResourceEvent;

readonly class ResourceCreateListener
{
    public function __construct(
        private MetadataPropertyFactoryInterface $propertyFactory,
    ) {
    }

    public function __invoke(ResourceEvent $event): void
    {
        $resource = $event->getResource();
        $operations = [];

        foreach ($resource->getOperations() as $operation) {
            if (\count($operation->getProperties()) === 0) {
                $operation = $operation->withProperties($this->propertyFactory->createProperties($resource->getDataClass()));
            }
            $operations[$operation->getName()] = $operation;
        }
        $resource = $resource->withOperations($operations);
        $event->setResource($resource);
    }
}
