<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\DataTransformer;

use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

interface DataTransformerInterface
{
    public function transform(PropertyInterface $property, mixed $data): mixed;
}
