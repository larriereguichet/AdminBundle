<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Locator;

use LAG\AdminBundle\Resource\Metadata\PropertyInterface;
use LAG\AdminBundle\Resource\Metadata\ResourceLink;

final readonly class PropertyLocator implements PropertyLocatorInterface
{
    public function locateProperties(\ReflectionClass $resourceClass): array
    {
        $properties = [];

        foreach ($resourceClass->getAttributes() as $reflectionAttribute) {
            $attribute = $reflectionAttribute->newInstance();

            if (!$attribute instanceof PropertyInterface) {
                continue;
            }
            $properties[] = $attribute;
        }

        foreach ($resourceClass->getProperties() as $reflectionProperty) {
            foreach ($reflectionProperty->getAttributes() as $reflectionAttribute) {
                $property = $reflectionAttribute->newInstance();

                if (!$property instanceof PropertyInterface) {
                    continue;
                }

                if (!$property->getName()) {
                    if ($property instanceof ResourceLink && $property->getOperation()) {
                        $property = $property->withName($property->getOperation());
                    } else {
                        $property = $property->withName($reflectionProperty->getName());
                    }
                }

                $properties[] = $property;
            }
        }

        return $properties;
    }
}
