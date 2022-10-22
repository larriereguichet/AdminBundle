<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\Property\PropertyInterface;

interface PropertyFactoryInterface
{
    public function create(PropertyInterface $property): PropertyInterface;
}
