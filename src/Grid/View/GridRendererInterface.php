<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use LAG\AdminBundle\Grid\GridView;
use LAG\AdminBundle\Metadata\Operation;

interface GridRendererInterface
{
    public function render(GridView $grid, Operation $operation): string;
}
