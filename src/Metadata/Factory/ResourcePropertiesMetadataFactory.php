<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\Attribute\Link;
use LAG\AdminBundle\Metadata\ResourceMetadataInterface;

use function Symfony\Component\String\u;

final readonly class ResourcePropertiesMetadataFactory implements ResourceMetadataFactoryInterface
{
    public function __construct(
        private ResourceMetadataFactoryInterface $metadataFactory,
        private PropertyCollectionMetadataFactoryInterface $propertyCollectionMetadataFactory,
    ) {
    }

    public function createMetadata(string $resourceName): ResourceMetadataInterface
    {
        $resource = $this->metadataFactory->createMetadata($resourceName);
        $properties = $this->propertyCollectionMetadataFactory->createMetadata($resource->getResourceClass());

        foreach ($properties as $index => $property) {
            if ($property->getLabel() === null) {
                if ($resource->getTranslationPattern()) {
                    $label = u($resource->getTranslationPattern())
                        ->replace('{application}', $resource->getApplicationName())
                        ->replace('{resource}', $resource->getShortName())
                        ->replace('{message}', u($property->getName())->snake()->toString())
                        ->lower()
                        ->toString()
                    ;
                } else {
                    $label = u($property->getName())
                        ->replace('_', ' ')
                        ->title()
                        ->toString()
                    ;
                }
                $property = $property->withLabel($label);
            }

            if (
                $property instanceof Link
                && $property->getOperation() !== null
                && !u($property->getOperation())->containsAny('.')
            ) {
                $property = $property->withOperation($resource->getApplicationName().'.'.$resource->getShortName().'.'.$property->getOperation());
            }
            $properties[$index] = $property;
        }

        return $resource->withProperties($properties);
    }
}
