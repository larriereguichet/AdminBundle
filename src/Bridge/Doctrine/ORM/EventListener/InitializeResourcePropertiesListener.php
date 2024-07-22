<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener;

use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataPropertyFactoryInterface;
use LAG\AdminBundle\Event\ResourceEvent;

final readonly class InitializeResourcePropertiesListener
{
    public function __construct(
        private MetadataPropertyFactoryInterface $propertyFactory,
    ) {
    }

    public function __invoke(ResourceEvent $event): void
    {
        $resource = $event->getResource();

        if ($resource->hasProperties()) {
            return;
        }
        $properties = $this->propertyFactory->createProperties($resource->getDataClass());

        $event->setResource($resource->withProperties($properties));
    }
}
