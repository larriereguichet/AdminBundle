<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\DataTransformer;

use LAG\AdminBundle\Metadata\Property\CountProperty;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;

class CountDataTransformer implements DataTransformerInterface
{
    public function supports(PropertyInterface $property, mixed $data): bool
    {
        return $property instanceof CountProperty;
    }

    public function transform(PropertyInterface $property, mixed $data): int
    {
        return \count($data);
    }
}
