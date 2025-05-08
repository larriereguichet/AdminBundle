<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\RowView;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\OperationInterface;

interface RowViewBuilderInterface
{
    public function buildHeadersRow(OperationInterface $operation, Grid $grid, array $context): RowView;

    public function buildRow(OperationInterface $operation, Grid $grid, mixed $data, array $context): RowView;
}
