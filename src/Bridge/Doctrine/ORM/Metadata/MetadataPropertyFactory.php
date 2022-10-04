<?php

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
        $fields = [];

        if ($metadata === null) {
            return $fields;
        }

        foreach ($metadata->getFieldNames() as $fieldName) {
            $fieldType = $metadata->getTypeOfField($fieldName);

            if (str_contains($fieldType, 'datetime')) {
                $fields[$fieldName] = new DateProperty($fieldName);
                continue;
            }

            if ($fieldType === 'boolean') {
                $fields[$fieldName] = new BooleanProperty($fieldName);
                continue;
            }
            $fields[$fieldName] = new StringProperty($fieldName);

            if (count($fields) > 10) {
                return $fields;
            }
        }

        foreach ($metadata->getAssociationNames() as $associationName) {
            $fields[$associationName] = new CountProperty($associationName);

            if (count($fields) > 10) {
                return $fields;
            }
        }


        return $fields;
    }


}
