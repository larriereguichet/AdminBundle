<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

interface CellViewBuilderInterface
{
    public function buildCell(
        OperationInterface $operation,
        Grid $grid,
        PropertyInterface $property,
        mixed $data,
        array $context = []
    ): CellView;
}
