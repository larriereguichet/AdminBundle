<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Grid;

interface GridViewBuilderInterface
{
    /**
     * Build a grid view for the given grid and operation.
     */
    public function build(
        Grid $grid,
        CollectionOperationInterface $operation,
        mixed $data,
        array $context = [],
    ): GridView;
}
