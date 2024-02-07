<?php

namespace LAG\AdminBundle\Resource\Locator;

use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;

class PropertyLocator implements PropertyLocatorInterface
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
                    $property = $property->withName($reflectionProperty->getName());
                }

                $properties[] = $property;
            }
        }

        return $properties;
    }
}
