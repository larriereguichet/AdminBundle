<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata;

use LAG\AdminBundle\Metadata\Property\BooleanProperty;
use LAG\AdminBundle\Metadata\Property\CountProperty;
use LAG\AdminBundle\Metadata\Property\DateProperty;
use LAG\AdminBundle\Metadata\Property\StringProperty;

class MetadataPropertyFactory implements MetadataPropertyFactoryInterface
{
    public function __construct(
        private MetadataHelperInterface $metadataHelper,
    ) {
    }

    public function createProperties(string $resourceClass): array
    {
        $metadata = $this->metadataHelper->findMetadata($resourceClass);
        $properties = [];

        if ($metadata === null) {
            return $properties;
        }

        foreach ($metadata->getFieldNames() as $fieldName) {
            $fieldType = $metadata->getTypeOfField($fieldName);

            if (str_contains($fieldType, 'datetime')) {
                $properties[$fieldName] = new DateProperty($fieldName);
            } elseif ($fieldType === 'boolean') {
                $properties[$fieldName] = new BooleanProperty($fieldName);
            } else {
                $properties[$fieldName] = new StringProperty($fieldName);
            }

            if (\count($properties) > 10) {
                return $properties;
            }
        }

        foreach ($metadata->getAssociationNames() as $associationName) {
            $properties[$associationName] = new CountProperty($associationName);

            if (\count($properties) > 10) {
                return $properties;
            }
        }

        return $properties;
    }
}
