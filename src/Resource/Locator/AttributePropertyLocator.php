<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Locator;

use LAG\AdminBundle\Metadata\PropertyInterface;

final readonly class AttributePropertyLocator implements PropertyLocatorInterface
{
    public function locateProperties(string $resourceClass): iterable
    {
        $reflectionClass = new \ReflectionClass($resourceClass);

        foreach ($reflectionClass->getAttributes(PropertyInterface::class, \ReflectionAttribute::IS_INSTANCEOF) as $reflectionAttribute) {
            $attribute = $reflectionAttribute->newInstance();
            yield $attribute;
        }

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            foreach ($reflectionProperty->getAttributes(PropertyInterface::class, \ReflectionAttribute::IS_INSTANCEOF) as $reflectionAttribute) {
                $property = $reflectionAttribute->newInstance();

                if (!$property->getName()) {
                    $property = $property->withName($reflectionProperty->getName());
                }
                yield $property;
            }
        }
    }
}
