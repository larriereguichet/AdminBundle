<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Initializer;

use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\Resource;

interface GridInitializerInterface
{
    public function initializeGrid(Resource $resource, CollectionOperationInterface $operation, Grid $grid): Grid;
}
