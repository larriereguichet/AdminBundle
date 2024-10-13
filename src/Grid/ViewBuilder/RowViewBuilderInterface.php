<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\RowView;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;

interface RowViewBuilderInterface
{
    public function buildHeadersRow(OperationInterface $operation, Grid $grid, array $context): RowView;

    public function buildRow(OperationInterface $operation, Grid $grid, mixed $data, array $context): RowView;
}
