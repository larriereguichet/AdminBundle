<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;

interface CellBuilderInterface
{
    /**
     * Build a cell view according to the underlying property and given data.
     *
     * @param array<string, mixed> $context
     */
    public function buildCell(
        OperationInterface $operation,
        GridInterface $grid,
        PropertyInterface $property,
        mixed $data,
        array $context = []
    ): CellView;
}
