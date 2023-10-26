<?php

namespace LAG\AdminBundle\Grid\Registry;

use LAG\AdminBundle\Grid\GridView;
use LAG\AdminBundle\Grid\GridInterface;

interface GridRegistryInterface
{
    public function get(string $name): GridInterface;

    public function has(string $name): bool;

    public function all(): iterable;
}
