<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Grid\GridView;
use Symfony\Contracts\EventDispatcher\Event;

class GridEvent extends Event
{
    public const GRID_CREATED = 'lag_admin.grid.created';

    public function __construct(
        private GridView $grid
    ) {
    }

    public function getGrid(): GridView
    {
        return $this->grid;
    }
}
