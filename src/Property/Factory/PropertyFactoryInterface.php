<?php

namespace LAG\AdminBundle\Property\Factory;

use LAG\AdminBundle\Metadata\Property\PropertyInterface;

interface PropertyFactoryInterface
{
    public function create(PropertyInterface $property): PropertyInterface;
}
