<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Initializer;

use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\GridInterface;

interface GridInitializerInterface
{
    public function initializeGrid(
        Resource $resource,
        CollectionOperationInterface $operation,
        GridInterface $grid,
    ): GridInterface;
}
