<?php

namespace LAG\AdminBundle\Grid\DataTransformer;

use LAG\AdminBundle\Metadata\Property\PropertyInterface;

interface DataTransformerInterface
{
    public function supports(PropertyInterface $property, mixed $data): bool;

    public function transform(PropertyInterface $property, mixed $data): mixed;
}
