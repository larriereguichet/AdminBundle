<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\Resource\MissingResourceException;
use LAG\AdminBundle\Metadata\ResourceMetadataInterface;

final readonly class ResourceMetadataFactory implements ResourceMetadataFactoryInterface
{
    public function __construct(
        private ResourceCollectionMetadataFactoryInterface $collectionMetadataFactory,
    ) {
    }

    public function createMetadata(string $resourceName): ResourceMetadataInterface
    {
        $resources = $this->collectionMetadataFactory->createMetadata();

        if (!\array_key_exists($resourceName, $resources)) {
            throw new MissingResourceException($resourceName);
        }
        $metadata = $resources[$resourceName];

        if ($metadata->getResourceClass() === null) {
            throw new Exception('The resource class is missing for the resource "%s"');
        }

        return $metadata;
    }
}
