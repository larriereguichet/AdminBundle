<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;

interface PropertyFactoryInterface
{
    /**
     * @return PropertyInterface[]
     */
    public function createCollection(Resource $resource): array;

    public function createProperty(Resource $resource, PropertyInterface $property): PropertyInterface;
}
