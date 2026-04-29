<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\RowView;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\GridInterface;

interface RowBuilderInterface
{
    /** @param array<string, mixed> $context */
    public function buildHeadersRow(CollectionOperationInterface $operation, GridInterface $grid, array $context = []): RowView;

    /** @param array<string, mixed> $context */
    public function buildRow(CollectionOperationInterface $operation, GridInterface $grid, mixed $data, array $context = []): RowView;
}
