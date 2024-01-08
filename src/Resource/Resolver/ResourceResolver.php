<?php

namespace LAG\AdminBundle\Resource\Resolver;

use LAG\AdminBundle\Resource\Locator\PropertyLocatorInterface;
use LAG\AdminBundle\Resource\Locator\ResourceLocatorInterface;

readonly class ResourceResolver implements ResourceResolverInterface
{
    public function __construct(
        private ClassResolverInterface $classResolver,
        private ResourceLocatorInterface $resourceLocator,
        private PropertyLocatorInterface $propertyLocator,
    ) {
    }

    public function resolveResources(array $directories): iterable
    {
        foreach ($directories as $directory) {
            $classes = $this->classResolver->resolveClasses($directory);

            foreach ($classes as $class) {
                $resources = $this->resourceLocator->locateResources($class);
                $properties = $this->propertyLocator->locateProperties($class);

                foreach ($resources as $resource) {
                    yield $resource->withProperties($properties);
                }
            }
        }
    }
}
