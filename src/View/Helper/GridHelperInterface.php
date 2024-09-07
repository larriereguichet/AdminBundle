<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Helper;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Grid\View\HeaderView;
use LAG\AdminBundle\Resource\Metadata\Operation;

interface GridHelperInterface
{
    public function renderGrid(GridView $grid, Operation $operation): string;

    public function renderHeader(HeaderView $header, array $options = []): string;

    public function renderCell(CellView $cell, array $options = []): string;
}
