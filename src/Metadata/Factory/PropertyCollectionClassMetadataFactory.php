<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\Attribute\Date;
use LAG\AdminBundle\Metadata\Attribute\Text;

final readonly class PropertyCollectionClassMetadataFactory implements PropertyCollectionMetadataFactoryInterface
{
    public function __construct(
        private PropertyCollectionMetadataFactoryInterface $metadataFactory,
    ) {
    }

    public function createMetadata(string $resourceClass): array
    {
        $properties = $this->metadataFactory->createMetadata($resourceClass);

        if ($properties !== []) {
            return $properties;
        }
        $reflectionClass = new \ReflectionClass($resourceClass);

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $property = match ((string) $reflectionProperty->getType()) {
                'string', 'integer', 'float' => new Text(name: $reflectionProperty->getName()),
                \DateTime::class, \DateTimeImmutable::class => new Date(name: $reflectionProperty->getName()),
                default => null,
            };

            if ($property === null) {
                continue;
            }
            $properties[] = $property;
        }

        return $properties;
    }
}
