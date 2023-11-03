<?php

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\UnexpectedTypeException;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Locator\MetadataLocatorInterface;

class ResourceResolver implements ResourceResolverInterface
{
    public function __construct(
        private array $resourcePaths,
        private MetadataLocatorInterface $locator,
        private ResourceFactoryInterface $resourceFactory,
    ) {
    }

    public function resolveResourceCollectionFromLocators(): iterable
    {
        foreach ($this->resourcePaths as $path) {
            $resources = $this->locator->locateCollection($path);

            foreach ($resources as $resource) {
                if (!$resource instanceof AdminResource) {
                    throw new UnexpectedTypeException($resource, AdminResource::class);
                }

                // The name is mandatory here
                if (!$resource->getName()) {
                    throw new Exception('The admin resource name should not be empty');
                }
                yield $this->resourceFactory->create($resource);
            }
        }
    }
}
