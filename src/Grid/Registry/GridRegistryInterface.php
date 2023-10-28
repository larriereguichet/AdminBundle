<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Registry;

use LAG\AdminBundle\Grid\GridInterface;

interface GridRegistryInterface
{
    public function get(string $name): GridInterface;

    public function has(string $name): bool;

    public function all(): iterable;
}
