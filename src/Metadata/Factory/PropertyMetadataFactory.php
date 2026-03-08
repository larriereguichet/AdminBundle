<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\Attribute\Link;
use LAG\AdminBundle\Metadata\PropertyMetadataInterface;

final class PropertyMetadataFactory implements PropertyMetadataFactoryInterface
{
    public function create(PropertyMetadataInterface $property): PropertyMetadataInterface
    {
        if ($property->getPropertyPath() === null) {
            $property = $property->withPropertyPath($property->getName());
        }

        if ($property->isSortable() && $property->getSortingPath() === null) {
            $sortingPath = $property->getName();

            if (\is_string($property->getPropertyPath())) {
                $sortingPath = $property->getPropertyPath();
            }
            $property = $property->withSortingPath($sortingPath);
        }

        if (($property instanceof Link) && $property->getText() === null) {
            if ($property->getTextPath() !== null) {
                $property = $property->withText(null);
            } else {
                $property = $property->withText($property->getName());
            }
        }

        return $property;
    }
}
