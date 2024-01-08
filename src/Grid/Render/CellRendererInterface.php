<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Render;

use LAG\AdminBundle\Grid\View\Cell;

interface CellRendererInterface
{
    public function render(Cell $cell, array $options = []): string;
}
