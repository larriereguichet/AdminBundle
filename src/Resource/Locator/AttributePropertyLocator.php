<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Locator;

use LAG\AdminBundle\Metadata\PropertyInterface;

final readonly class AttributePropertyLocator implements PropertyLocatorInterface
{
    public function locateProperties(string $resourceClass): iterable
    {
        $reflectionClass = new \ReflectionClass($resourceClass);

        foreach ($reflectionClass->getAttributes() as $reflectionAttribute) {
            $attribute = $reflectionAttribute->newInstance();

            if ($attribute instanceof PropertyInterface) {
                yield $attribute;
            }
        }

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            foreach ($reflectionProperty->getAttributes() as $reflectionAttribute) {
                $property = $reflectionAttribute->newInstance();

                if (!$property instanceof PropertyInterface) {
                    continue;
                }

                if (!$property->getName()) {
                    $property = $property->withName($reflectionProperty->getName());
                }
                yield $property;
            }
        }
    }
}
