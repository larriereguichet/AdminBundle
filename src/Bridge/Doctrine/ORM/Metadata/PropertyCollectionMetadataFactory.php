<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata;

use Doctrine\Bundle\DoctrineBundle\Registry;
use LAG\AdminBundle\Metadata\Attribute\Boolean;
use LAG\AdminBundle\Metadata\Attribute\Date;
use LAG\AdminBundle\Metadata\Attribute\RichText;
use LAG\AdminBundle\Metadata\Attribute\Text;
use LAG\AdminBundle\Metadata\Factory\PropertyCollectionMetadataFactoryInterface;

final readonly class PropertyCollectionMetadataFactory implements PropertyCollectionMetadataFactoryInterface
{
    public function __construct(
        private PropertyCollectionMetadataFactoryInterface $metadataFactory,
        private Registry $registry,
    ) {
    }

    public function createMetadata(string $resourceClass): array
    {
        $properties = $this->metadataFactory->createMetadata($resourceClass);
        $manager = $this->registry->getManagerForClass($resourceClass);

        if ($properties !== [] || $manager === null) {
            return $properties;
        }
        $doctrineMetadata = $manager->getClassMetadata($resourceClass);

        foreach ($doctrineMetadata->getFieldNames() as $fieldName) {
            $fieldType = $doctrineMetadata->getTypeOfField($fieldName);

            $property = match ($fieldType) {
                'string' => new Text(name: $fieldName),
                'text' => new RichText(name: $fieldName),
                'boolean' => new Boolean(name: $fieldName),
                'date', 'datetime', 'date_immutable', 'datetime_immutable' => new Date(name: $fieldName),
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
