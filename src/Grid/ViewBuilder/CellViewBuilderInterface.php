<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

interface CellViewBuilderInterface
{
    public function buildCell(Grid $grid, PropertyInterface $property, mixed $data, array $context = []): CellView;
}
