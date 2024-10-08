<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata;

use LAG\AdminBundle\Resource\Metadata\Boolean;
use LAG\AdminBundle\Resource\Metadata\Count;
use LAG\AdminBundle\Resource\Metadata\Date;
use LAG\AdminBundle\Resource\Metadata\Text;

final readonly class MetadataPropertyFactory implements MetadataPropertyFactoryInterface
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
                $properties[$fieldName] = new Date($fieldName);
            } elseif ($fieldType === 'boolean') {
                $properties[$fieldName] = new Boolean($fieldName);
            } else {
                $properties[$fieldName] = new Text($fieldName);
            }

            if (\count($properties) > 10) {
                return $properties;
            }
        }

        foreach ($metadata->getAssociationNames() as $associationName) {
            if (!$metadata->isCollectionValuedAssociation($associationName)) {
                continue;
            }
            $properties[$associationName] = new Count($associationName);

            if (\count($properties) > 10) {
                return $properties;
            }
        }

        return $properties;
    }
}
