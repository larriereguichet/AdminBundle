<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Grid\View\Cell;
use LAG\AdminBundle\Grid\View\Header;

interface CellBuilderInterface
{
    /**
     * Build a cell view according to the underlying property and given data.
     *
     * @param OperationInterface $operation
     * @param Grid $grid
     * @param PropertyInterface $property
     * @param mixed $data
     * @param array<string, mixed> $context
     *
     * @return Cell
     */
    public function buildCell(
        OperationInterface $operation,
        GridInterface $grid,
        PropertyInterface $property,
        mixed $data,
        array $context = []
    ): Cell;

    /**
     * Build cell header. It is useful for some grid like table grids.
     *
     * @param OperationInterface $operation
     * @param Grid $grid
     * @param PropertyInterface $property
     * @param array<string, mixed> $context
     *
     * @return Header
     */
    public function buildHeader(
        OperationInterface $operation,
        GridInterface $grid,
        PropertyInterface $property,
        array $context = []
    ): Header;
}
