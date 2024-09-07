<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\RowView;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\Resource;

interface RowViewBuilderInterface
{
    public function buildHeadersRow(Grid $grid, Resource $resource, array $context): RowView;

    public function buildRow(Grid $grid, Resource $resource, mixed $data, array $context): RowView;
}
