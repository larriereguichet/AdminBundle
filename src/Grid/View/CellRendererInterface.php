<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use LAG\AdminBundle\Grid\Cell;

interface CellRendererInterface
{
    public function render(Cell $cell): string;
}
