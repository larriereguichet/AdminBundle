<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;

interface GridViewBuilderInterface
{
    public function build(Grid $grid, OperationInterface $operation, mixed $data, array $context = []): GridView;
}
