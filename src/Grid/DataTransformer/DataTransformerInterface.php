<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\DataTransformer;

use LAG\AdminBundle\Metadata\PropertyInterface;

interface DataTransformerInterface
{
    public function transform(PropertyInterface $property, mixed $data): mixed;
}
