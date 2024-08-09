<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Registry;

use LAG\AdminBundle\Resource\Metadata\Grid;

interface GridRegistryInterface
{
    public function add(Grid $grid): void;

    public function get(string $gridName): Grid;

    public function has(string $gridName): bool;

    public function remove(string $gridName): void;

    public function all(): iterable;
}
