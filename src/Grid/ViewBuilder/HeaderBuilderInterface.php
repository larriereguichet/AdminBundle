<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\HeaderView;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;

interface HeaderBuilderInterface
{
    /**
     * Build cell header. It is useful for some grid like table grids.
     *
     * @param array<string, mixed> $context
     */
    public function buildHeader(
        OperationInterface $operation,
        GridInterface $grid,
        PropertyInterface $property,
        array $context = []
    ): HeaderView;
}
