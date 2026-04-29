<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\GridInterface;

interface GridBuilderInterface
{
    /**
     * Build a grid view for the given grid and operation.
     *
     * @param GridInterface $grid The grid metadata definition
     * @param CollectionOperationInterface $operation The associated operation
     * @param iterable<int|string, mixed> $data Iterable data. Usually an array or a collection of objects
     * @param array<string, mixed> $context An additional runtime context
     */
    public function build(
        GridInterface $grid,
        CollectionOperationInterface $operation,
        iterable $data,
        array $context = [],
    ): GridView;
}
