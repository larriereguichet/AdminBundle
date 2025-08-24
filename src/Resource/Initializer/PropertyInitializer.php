<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Initializer;

use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Metadata\Resource;

use function Symfony\Component\String\u;

final class PropertyInitializer implements PropertyInitializerInterface
{
    public function initializeProperty(Resource $resource, PropertyInterface $property): PropertyInterface
    {
        if ($property->getPropertyPath() === null) {
            $property = $property->withPropertyPath($property->getName());
        }

        if ($property->getLabel() === null) {
            if ($resource->getTranslationPattern()) {
                $label = u($resource->getTranslationPattern())
                    ->replace('{application}', $resource->getApplication())
                    ->replace('{resource}', $resource->getName())
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

        if (!$property->getTranslationDomain()) {
            $property = $property->withTranslationDomain($resource->getTranslationDomain());
        }

        if ($property instanceof Link) {
            if ($property->getText() === null) {
                if ($property->getTextPath() !== null) {
                    $property = $property->withText(null);
                } else {
                    $property = $property->withText($property->getName());
                }
            }

            if ($property->getOperation() !== null && !u($property->getOperation())->containsAny('.')) {
                $property = $property->withOperation($resource->getApplication().'.'.$resource->getName().'.'.$property->getOperation());
            }
        }

        if ($property->isSortable() && $property->getSortingPath() === null) {
            $sortingPath = $property->getName();

            if (\is_string($property->getPropertyPath())) {
                $sortingPath = $property->getPropertyPath();
            }
            $property = $property->withSortingPath($sortingPath);
        }

        return $property;
    }
}
