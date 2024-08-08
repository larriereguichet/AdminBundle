<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Grid\View\HeaderView;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

interface CellViewBuilderInterface
{
    public function buildHeader(Grid $grid, PropertyInterface $property, array $context = []): HeaderView;

    public function build(Grid $grid, PropertyInterface $property, mixed $data, array $context = []): CellView;
}
