<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\DataTransformer;

interface DataTransformerInterface
{
    public function transform(mixed $data): mixed;
}
