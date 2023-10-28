<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

interface TransformablePropertyInterface
{
    public function transform(mixed $data): mixed;
}
