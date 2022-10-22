<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use LAG\AdminBundle\Grid\Grid;
use LAG\AdminBundle\Metadata\Operation;

interface GridRendererInterface
{
    public function render(Grid $grid, Operation $operation): string;
}
