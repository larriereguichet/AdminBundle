<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Registry;

use LAG\AdminBundle\Grid\DataTransformer\DataTransformerInterface;

interface DataTransformerRegistryInterface
{
    public function get(string $dataTransformer): DataTransformerInterface;

    public function has(string $dataTransformer): bool;
}
