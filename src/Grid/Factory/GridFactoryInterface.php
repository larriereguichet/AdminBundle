<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Factory;

use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Grid;

/**
 * Create a new grid instance.
 */
interface GridFactoryInterface
{
    public function createGrid(CollectionOperationInterface $operation): Grid;
}
