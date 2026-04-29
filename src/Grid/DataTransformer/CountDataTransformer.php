<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\DataTransformer;

use LAG\AdminBundle\Metadata\PropertyInterface;

final readonly class CountDataTransformer implements DataTransformerInterface
{
    public function transform(PropertyInterface $property, mixed $data): int
    {
        return \count($data);
    }
}
