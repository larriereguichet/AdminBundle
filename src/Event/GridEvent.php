<?php

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Grid\Grid;
use Symfony\Contracts\EventDispatcher\Event;

class GridEvent extends Event
{
    public const GRID_CREATED = 'lag_admin.grid.created';

    public function __construct(
        private Grid $grid
    ) {
    }

    public function getGrid(): Grid
    {
        return $this->grid;
    }
}
