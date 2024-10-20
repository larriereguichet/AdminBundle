<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\HeaderView;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

interface HeaderViewBuilderInterface
{
    public function buildHeader(
        OperationInterface $operation,
        Grid $grid,
        PropertyInterface $property,
        array $context = []
    ): HeaderView;
}
