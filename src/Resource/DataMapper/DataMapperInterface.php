<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\DataMapper;

use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

interface DataMapperInterface
{
    public function getValue(PropertyInterface $property, mixed $data): mixed;
}
