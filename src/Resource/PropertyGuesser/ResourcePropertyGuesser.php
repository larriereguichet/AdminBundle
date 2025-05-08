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

    public function guessProperties(Resource $resource): iterable
    {
        if ($resource->getResourceClass() === null) {
            return [];
        }
        $reflectionClass = new \ReflectionClass($resource->getResourceClass());

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $property = $this->propertyGuesser->guessProperty(
                $resource->getResourceClass(),
                $reflectionProperty->getName(),
                (string) $reflectionProperty->getType(),
            );

            if ($property === null) {
                continue;
            }
            yield $property;
        }
    }
}
