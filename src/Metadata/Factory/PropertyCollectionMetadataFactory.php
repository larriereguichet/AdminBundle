<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\PropertyMetadataInterface;

final readonly class PropertyCollectionMetadataFactory implements PropertyCollectionMetadataFactoryInterface
{
    public function __construct(
        private PropertyMetadataFactoryInterface $propertyMetadataFactory,
    ) {
    }

    public function createMetadata(string $resourceClass): array
    {
        $reflectionClass = new \ReflectionClass($resourceClass);
        $properties = [];

        foreach ($reflectionClass->getAttributes(PropertyMetadataInterface::class, \ReflectionAttribute::IS_INSTANCEOF) as $reflectionAttribute) {
            $property = $reflectionAttribute->newInstance();
            $properties[] = $this->propertyMetadataFactory->create($property);
        }

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            foreach ($reflectionProperty->getAttributes(PropertyMetadataInterface::class, \ReflectionAttribute::IS_INSTANCEOF) as $reflectionAttribute) {
                $property = $reflectionAttribute->newInstance();

                if (!$property->getName()) {
                    $property = $property->withName($reflectionProperty->getName());
                }
                $properties[] = $this->propertyMetadataFactory->create($property);
            }
        }

        return $properties;
    }
}
