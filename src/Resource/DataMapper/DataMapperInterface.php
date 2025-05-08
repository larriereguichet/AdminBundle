<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\DataMapper;

use LAG\AdminBundle\Metadata\PropertyInterface;

/**
 * Map resource data to a property data. Use the property path to retrieve the property data. The property path should
 * follow the PropertyAccess syntax. A property path set to "true" returns the resource data. A property path set to
 * "false" returns null.
 */
interface DataMapperInterface
{
    public function getValue(PropertyInterface $property, mixed $data): mixed;
}
