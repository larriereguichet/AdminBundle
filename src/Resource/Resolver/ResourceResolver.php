<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Resolver;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\Locator\PropertyLocatorInterface;
use LAG\AdminBundle\Resource\Locator\ResourceLocatorInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

use function Symfony\Component\String\u;

final readonly class ResourceResolver implements ResourceResolverInterface
{
    public function __construct(
        private KernelInterface $kernel,
        private ClassResolverInterface $classResolver,
        private ResourceLocatorInterface $resourceLocator,
        private PropertyLocatorInterface $propertyLocator,
        private PhpFileResolverInterface $fileResolver,
    ) {
    }

    public function resolveResources(array $directories): iterable
    {
        foreach ($directories as $directory) {
            if (u($directory)->startsWith('@')) {
                $directory = $this->kernel->locateResource($directory);
            }
            $finder = new Finder();
            $finder->files()
                ->in($directory)
                ->name('*.php')
                ->sortByName(true)
            ;

            foreach ($finder as $fileInfo) {
                if (!$fileInfo->isReadable() || !$fileInfo->isFile()) {
                    continue;
                }
                $class = $this->classResolver->resolveClass($fileInfo->getRealPath());

                if ($class !== null) {
                    $resources = $this->resourceLocator->locateResources($class);
                    $properties = $this->propertyLocator->locateProperties($class);

                    foreach ($resources as $resource) {
                        yield $resource->withProperties($properties);
                    }

                    continue;
                }
                $resources = $this->fileResolver->resolveFile($fileInfo->getRealPath());

                if (!is_iterable($resources)) {
                    continue;
                }

                foreach ($resources as $resource) {
                    if (!$resource instanceof Resource) {
                        throw new Exception(\sprintf('The file "%s" should return an iterable of "%s", got "%s"', $fileInfo->getRealPath(), Resource::class, get_debug_type($resource)));
                    }

                    if ($resource->getDataClass() !== null) {
                        $properties = $this->propertyLocator->locateProperties(new \ReflectionClass($resource->getDataClass()));
                    }

                    yield $resource->withProperties($properties ?? []);
                }
            }
        }
    }
}
