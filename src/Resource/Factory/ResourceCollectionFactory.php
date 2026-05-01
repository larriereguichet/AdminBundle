<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Metadata\Factory\ResourceCollectionMetadataFactoryInterface;

final readonly class ResourceCollectionFactory implements ResourceCollectionFactoryInterface
{
    public function __construct(
        private ResourceCollectionMetadataFactoryInterface $metadataCollectionFactory,
        private ResourceFactoryInterface $resourceFactory,
    ) {
    }

    public function create(): array
    {
        $metadataCollection = $this->metadataCollectionFactory->createMetadata();
        $resources = [];

        foreach (array_keys($metadataCollection) as $resourceName) {
            $resources[$resourceName] = $this->resourceFactory->create($resourceName);
        }

        return $resources;
    }
}
