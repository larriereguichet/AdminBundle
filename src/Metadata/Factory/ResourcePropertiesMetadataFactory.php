<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\Attribute\Link;
use LAG\AdminBundle\Metadata\PropertyMetadataInterface;
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
        $properties = array_merge(
            $this->propertyCollectionMetadataFactory->createMetadata($resource->getResourceClass()),
            $resource->getProperties(),
        );
        $newProperties = [];

        foreach ($properties as $property) {
            \assert($property instanceof PropertyMetadataInterface);
            $label = $property->getLabel();
            $sortingPath = $property->getSortingPath();

            if ($label === null) {
                $label = u($property->getName())
                    ->replace('_', ' ')
                    ->title()
                    ->toString()
                ;

                if ($resource->getTranslationPattern()) {
                    $label = u($resource->getTranslationPattern())
                        ->replace('{application}', $resource->getApplication())
                        ->replace('{resource}', $resource->getShortName())
                        ->replace('{message}', u($property->getName())->snake()->toString())
                        ->lower()
                        ->toString()
                    ;
                }
            }

            if ($sortingPath === null && $property->isSortable()) {
                $sortingPath = $property->getName();

                if (\is_string($property->getPropertyPath())) {
                    $sortingPath = $property->getPropertyPath();
                }
            }
            $property = $property
                ->withLabel($label)
                ->withPropertyPath($property->getPropertyPath() ?? $property->getName())
                ->withSortingPath($sortingPath)
            ;

            if ($property instanceof Link) {
                $operation = $property->getOperation();

                if ($operation === null || !u($operation)->containsAny('.')) {
                    $operation = $resource->getApplication().'.'.$resource->getShortName().'.'.$property->getOperation();
                }
                $property = $property
                    ->withText($property->getText() ?? $property->getName())
                    ->withOperation($operation)
                    ->withPropertyPath($property->getPropertyPath() ?? $property->getName())
                ;
            }
            $newProperties[$property->getName()] = $property;
        }

        return $resource->withProperties($newProperties);
    }
}
