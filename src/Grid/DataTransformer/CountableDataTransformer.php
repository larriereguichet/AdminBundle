<?php

namespace LAG\AdminBundle\Grid\DataTransformer;

use LAG\AdminBundle\Metadata\Property\PropertyInterface;

readonly class CountableDataTransformer implements PropertyDataTransformerInterface
{
    public function supports(PropertyInterface $property, mixed $data): bool
    {
        return $property->getAllowedDataType() === 'countable';
    }

    public function transform(PropertyInterface $property, mixed $data): int
    {
        return count($data);
    }
}
