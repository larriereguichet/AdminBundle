<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\DataTransformer;

use LAG\AdminBundle\Exception\UnexpectedTypeException;
use LAG\AdminBundle\Metadata\PropertyInterface;

final readonly class EnumDataTransformer implements DataTransformerInterface
{
    public function transform(PropertyInterface $property, mixed $data): string|int
    {
        if (!$data instanceof \BackedEnum) {
            throw new UnexpectedTypeException($data, \BackedEnum::class);
        }

        return $data->value;
    }
}
