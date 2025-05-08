<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

interface TransformablePropertyInterface
{
    public function transform(mixed $data): mixed;
}
