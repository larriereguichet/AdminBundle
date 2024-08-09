<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Resolver;

use LAG\AdminBundle\Resource\Locator\PropertyLocatorInterface;
use LAG\AdminBundle\Resource\Locator\ResourceLocatorInterface;
use Symfony\Component\Finder\Finder;

final readonly class ResourceResolver implements ResourceResolverInterface
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
            $finder = new Finder();
            $finder->files()
                ->in($directory)
                ->name('*.php')
                ->sortByName(true)
            ;

            foreach ($finder as $fileInfo) {
                if (!$fileInfo->isReadable()) {
                    continue;
                }
                $class = null;

                if ($fileInfo->isFile()) {
                    $class = $this->classResolver->resolveClass($fileInfo->getRealPath());
                }

                if ($class === null) {
                    continue;
                }
                $resources = $this->resourceLocator->locateResources($class);
                $properties = $this->propertyLocator->locateProperties($class);

                foreach ($resources as $resource) {
                    yield $resource->withProperties($properties);
                }
            }
        }
    }
}
