<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\PropertyGuesser;

use LAG\AdminBundle\Metadata\Resource;

final readonly class ResourcePropertyGuesser implements ResourcePropertyGuesserInterface
{
    public function __construct(
        private PropertyGuesserInterface $propertyGuesser,
    ) {
    }

    public function guessProperties(Resource $resource): array
    {
        if ($resource->getResourceClass() === null) {
            return [];
        }
        $reflectionClass = new \ReflectionClass($resource->getResourceClass());
        $properties = [];

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $property = $this->propertyGuesser->guessProperty(
                $resource->getResourceClass(),
                $reflectionProperty->getName(),
                (string) $reflectionProperty->getType(),
            );

            if ($property === null) {
                continue;
            }
            $properties[] = $property;
        }

        return $properties;
    }
}
