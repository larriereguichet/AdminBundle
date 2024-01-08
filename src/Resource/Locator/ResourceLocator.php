<?php

namespace LAG\AdminBundle\Resource\Locator;

use LAG\AdminBundle\Metadata\Resource;
use Symfony\Component\Finder\Finder;
use function Symfony\Component\String\u;

readonly class ResourceLocator implements ResourceLocatorInterface
{
    public function __construct(
        private string $defaultApplication,
    ) {
    }

    public function locateResources(\ReflectionClass $resourceClass): iterable
    {
        $attributes = $resourceClass->getAttributes(Resource::class);

        foreach ($attributes as $attribute) {
            /** @var Resource $resource */
            $resource = $attribute->newInstance();

            if (!$resource->getName()) {
                $resource = $resource->withName(
                    u($resourceClass->getShortName())
                        ->snake()
                        ->lower()
                        ->toString()
                );
            }

            if (!$resource->getApplicationName()) {
                $resource = $resource->withApplicationName($this->defaultApplication);
            }

            if (!$resource->getDataClass()) {
                $resource = $resource->withDataClass($resourceClass->getName());
            }

            yield $resource;
        }
    }
}
