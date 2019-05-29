<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Exception;
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

        // As the Doctrine ClassMetadata interface does not expose any properties, we should check the instance of the
        // returned metadata class to respect the good practices
        if (!$metadata instanceof ClassMetadataInfo) {
            return [];
        }
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

    /**
     * Return the Doctrine metadata of the given class.
     *
     * @param $class
     *
     * @return ClassMetadata|null
     */
    public function findMetadata($class): ?ClassMetadata
    {
        $metadata = null;

        try {
            // We could not use the hasMetadataFor() method as it is not working if the entity is not loaded. But
            // the getMetadataFor() method could throw an exception if the class is not found
            $metadata = $this->entityManager->getMetadataFactory()->getMetadataFor($class);
        } catch (Exception $exception) {
            // If an exception is raised, nothing to do. Extra data from metadata will be not used.
        }

        return $metadata;
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
