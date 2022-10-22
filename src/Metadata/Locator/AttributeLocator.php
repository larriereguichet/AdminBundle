<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Locator;

use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\AttributesHelper;

use function Symfony\Component\String\u;

class AttributeLocator implements MetadataLocatorInterface
{
    public function locateCollection(string $resourceDirectory): iterable
    {
        $classes = AttributesHelper::getReflectionClassesFromDirectories($resourceDirectory);
        $resources = [];

        foreach ($classes as $reflectionClass) {
            $attributes = $reflectionClass->getAttributes(AdminResource::class);

            foreach ($attributes as $attribute) {
                /** @var AdminResource $resource */
                $resource = $attribute->newInstance();
                $resource = $resource->withDataClass($reflectionClass->getParentClass());

                if (!$resource->getName()) {
                    $resource = $resource->withName(
                        u($reflectionClass->getName())
                            ->afterLast('\\')
                            ->snake()
                            ->lower()
                            ->toString()
                    );
                }

                if (!$resource->getDataClass()) {
                    $resource = $resource->withDataClass($reflectionClass->getName());
                }
                $resources[] = $resource;
            }
        }

        return $resources;
    }
}
