<?php

namespace LAG\AdminBundle\Grid\View;

use LAG\AdminBundle\Grid\Cell;

interface CellRendererInterface
{
    public function render(Cell $cell): string;
}
