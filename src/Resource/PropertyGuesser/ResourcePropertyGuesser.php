<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\PropertyGuesser;

use LAG\AdminBundle\Resource\Metadata\Resource;

final readonly class ResourcePropertyGuesser implements ResourcePropertyGuesserInterface
{
    public function __construct(
        private PropertyGuesserInterface $propertyGuesser,
    ) {
    }

    public function guessProperties(Resource $resource): iterable
    {
        if ($resource->getDataClass() === null) {
            return [];
        }
        $reflectionClass = new \ReflectionClass($resource->getDataClass());

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $property = $this->propertyGuesser->guessProperty(
                $resource->getDataClass(),
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
