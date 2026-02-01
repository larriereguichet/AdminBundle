<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Initializer;

use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\PropertyInterface;

interface PropertyInitializerInterface
{
    public function initializeProperty(Resource $resource, PropertyInterface $property): PropertyInterface;
}
