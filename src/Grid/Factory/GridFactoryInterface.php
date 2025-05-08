<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Factory;

use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\OperationInterface;

/**
 * Create a new grid instance.
 */
interface GridFactoryInterface
{
    public function createGrid(string $gridName, OperationInterface $operation): Grid;
}
