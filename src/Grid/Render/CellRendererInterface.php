<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Render;

use LAG\AdminBundle\Grid\View\CellView;

interface CellRendererInterface
{
    public function render(CellView $cell, array $options = []): string;
}
