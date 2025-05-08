<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\DataTransformer;

use LAG\AdminBundle\Metadata\Map;
use LAG\AdminBundle\Metadata\PropertyInterface;

// TODO keep ?
readonly class MapDataTransformer implements DataTransformerInterface
{
    public function supports(PropertyInterface $property, mixed $data): bool
    {
        return false;

        // return $property instanceof Map;
    }

    public function transform(PropertyInterface $property, mixed $data): mixed
    {
        \assert($property instanceof Map);

        if (!\is_string($data) || !\array_key_exists($data, $property->getMap())) {
            return null;
        }

        return $property->getMap()[$data];
    }
}
