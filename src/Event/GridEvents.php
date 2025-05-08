<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

class GridEvents
{
    public const string GRID_EVENT_TEMPLATE = '{application}.{resource}.grid';
    public const string GRID_EVENT = 'lag_admin.resource.grid';
}
