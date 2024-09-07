<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Render;

use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Resource\Metadata\Operation;

interface GridRendererInterface
{
    public function render(GridView $grid, Operation $operation, array $options = []): string;
}
