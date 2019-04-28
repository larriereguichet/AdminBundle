<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LAG\AdminBundle\Field\Definition\FieldDefinition;

class MetadataHelper implements MetadataHelperInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFields(string $entityClass): array
    {
        $metadata = $this->entityManager->getClassMetadata($entityClass);
        $fieldNames = (array) $metadata->fieldNames;
        $fields = [];

        foreach ($fieldNames as $fieldName) {
            // Remove the primary key field if it's not managed manually
            if (!$metadata->isIdentifierNatural() && in_array($fieldName, $metadata->identifier)) {
                continue;
            }
            $mapping = $metadata->getFieldMapping($fieldName);
            $formOptions = [];

            // When a field is defined as nullable in the Doctrine entity configuration, the associated form field
            // should not be required neither
            if (key_exists('nullable', $mapping) && true === $mapping['nullable']) {
                $formOptions['required'] = false;
            }
            $fields[$fieldName] = new FieldDefinition($metadata->getTypeOfField($fieldName), [], $formOptions);
        }

        foreach ($metadata->associationMappings as $fieldName => $relation) {
            $formOptions = [];
            $formType = 'choice';

            if (ClassMetadataInfo::MANY_TO_MANY === $relation['type']) {
                $formOptions['expanded'] = true;
                $formOptions['multiple'] = true;
            }
            if ($this->isJoinColumnNullable($relation)) {
                $formOptions['required'] = false;
            }
            $fields[$fieldName] = new FieldDefinition($formType, [], $formOptions);
        }

        return $fields;
    }

    private function isJoinColumnNullable(array $relation)
    {
        if (!key_exists('joinColumns', $relation)) {
            return false;
        }

        if (!key_exists('nullable', $relation['joinColumns'])) {
            return false;
        }

        return false === $relation['joinColumns']['nullable'];
    }
}
