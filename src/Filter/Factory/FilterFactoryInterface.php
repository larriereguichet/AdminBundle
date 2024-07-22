<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Filter\Factory;

use LAG\AdminBundle\Filter\FilterInterface;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

interface FilterFactoryInterface
{
    public function create(FilterInterface $filter): FilterInterface;

    public function createFromProperty(PropertyInterface $property): FilterInterface;
}
