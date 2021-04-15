<?php

namespace LAG\AdminBundle\Bridge\Doctrine\Event\Listener;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Event\Events\FieldDefinitionEvent;
use LAG\AdminBundle\Field\Definition\FieldDefinition;

class CreateFieldDefinitionListener
{
    private MetadataHelperInterface $metadataHelper;

    public function __construct(MetadataHelperInterface $metadataHelper)
    {
        $this->metadataHelper = $metadataHelper;
    }

    public function __invoke(FieldDefinitionEvent $event): void
    {
        $metadata = $this->metadataHelper->findMetadata($event->getClass());

        // As the Doctrine ClassMetadata interface does not expose any properties, we should check the instance of the
        // returned metadata class to respect the good practices
        if (!$metadata instanceof ClassMetadataInfo) {
            return;
        }
        $fieldNames = (array) $metadata->fieldNames;

        foreach ($fieldNames as $fieldName) {
            // Remove the primary key field if it's not managed manually
            if (!$metadata->isIdentifierNatural() && \in_array($fieldName, $metadata->identifier)) {
                continue;
            }
            $mapping = $metadata->getFieldMapping($fieldName);
            $formOptions = [];

            // When a field is defined as nullable in the Doctrine entity configuration, the associated form field
            // should not be required neither
            if (\array_key_exists('nullable', $mapping) && true === $mapping['nullable']) {
                $formOptions['required'] = false;
            }
            $event->addDefinition($fieldName, new FieldDefinition($metadata->getTypeOfField($fieldName), [], $formOptions));
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
            $event->addDefinition($fieldName, new FieldDefinition($formType, [], $formOptions));
        }
    }

    private function isJoinColumnNullable(array $relation): bool
    {
        if (!\array_key_exists('joinColumns', $relation)) {
            return false;
        }

        if (!\array_key_exists('nullable', $relation['joinColumns'])) {
            return false;
        }

        return false === $relation['joinColumns']['nullable'];
    }
}
