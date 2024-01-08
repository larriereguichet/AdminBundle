<?php

namespace LAG\AdminBundle\Grid\DataTransformer;

use LAG\AdminBundle\Metadata\Property\Mapped;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;

readonly class MapDataTransformer implements PropertyDataTransformerInterface
{
    public function supports(PropertyInterface $property, mixed $data): bool
    {
        return $property instanceof Mapped;
    }

    public function transform(PropertyInterface $property, mixed $data): mixed
    {
        assert($property instanceof Mapped);

        if (!is_string($data) || !array_key_exists($data, $property->getMap())) {
            return null;
        }

        return $property->getMap()[$data];
    }
}
