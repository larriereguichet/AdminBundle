<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Grid\View\Row;

interface RowBuilderInterface
{
    /** @param array<string, mixed> $context */
    public function buildHeadersRow(OperationInterface $operation, GridInterface $grid, array $context = []): Row;

    /** @param array<string, mixed> $context */
    public function buildRow(OperationInterface $operation, GridInterface $grid, mixed $data, array $context = []): Row;
}
