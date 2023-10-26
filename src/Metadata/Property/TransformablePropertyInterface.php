<?php

namespace LAG\AdminBundle\Metadata\Property;

interface TransformablePropertyInterface
{
    public function transform(mixed $data): mixed;
}
