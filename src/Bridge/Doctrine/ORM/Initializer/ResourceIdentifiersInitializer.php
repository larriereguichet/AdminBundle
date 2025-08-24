<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Initializer;

use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Metadata\Resource;

final readonly class ResourceIdentifiersInitializer implements ResourceIdentifiersInitializerInterface
{
    public function __construct(
        private MetadataHelperInterface $metadataHelper,
    ) {
    }

    public function initializeResourceIdentifiers(Resource $resource): Resource
    {
        if ($resource->getIdentifiers() !== null) {
            return $resource;
        }
        $metadata = $this->metadataHelper->findMetadata($resource->getResourceClass());
        $identifiers = [];

        if ($metadata !== null) {
            $identifiers = $metadata->getIdentifier();
        }

        return $resource->withIdentifiers($identifiers);
    }
}
