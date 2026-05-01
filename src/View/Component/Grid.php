<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component;

use LAG\AdminBundle\Grid\View;

class Grid
{
    public View\GridView $grid;
    public mixed $data;
}
