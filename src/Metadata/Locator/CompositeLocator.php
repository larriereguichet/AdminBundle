<?php

namespace LAG\AdminBundle\Metadata\Locator;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\AdminResource;

class CompositeLocator implements MetadataLocatorInterface
{
    public function __construct(
        private iterable $locators,
    ) {
    }

    public function locateCollection(string $resourceDirectory): array
    {
        $resources = [];

        /** @var MetadataLocatorInterface $locator */
        foreach ($this->locators as $locator) {
            foreach ($locator->locateCollection($resourceDirectory) as $resource) {
                if (!$resource instanceof AdminResource) {
                    throw new Exception(sprintf(
                        'The locator "%s" returns an instance of "%s", expected an instance of "%s"',
                        get_class($locator),
                        get_class($resource),
                        AdminResource::class,
                    ));
                }
                $resources[] = $resource;
            }
        }

        return $resources;
    }
}
