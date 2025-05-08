<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener;

use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Event\ResourceEvent;

final readonly class InitializeResourceIdentifiersListener
{
    public function __construct(
        private MetadataHelperInterface $metadataHelper,
    ) {
    }

    public function __invoke(ResourceEvent $event): void
    {
        $resource = $event->getResource();

        if ($resource->getIdentifiers() !== null) {
            return;
        }
        $metadata = $this->metadataHelper->findMetadata($resource->getResourceClass());
        $identifiers = [];

        if ($metadata !== null) {
            $identifiers = $metadata->getIdentifier();
        }

        $event->setResource($resource->withIdentifiers($identifiers));
    }
}
