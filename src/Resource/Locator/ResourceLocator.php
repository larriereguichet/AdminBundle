<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Locator;

use LAG\AdminBundle\Resource\Metadata\Resource;

use function Symfony\Component\String\u;

final readonly class ResourceLocator implements ResourceLocatorInterface
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

            if (!$resource->getApplication()) {
                $resource = $resource->withApplication($this->defaultApplication);
            }

            if (!$resource->getDataClass()) {
                $resource = $resource->withDataClass($resourceClass->getName());
            }

            yield $resource;
        }
    }
}
