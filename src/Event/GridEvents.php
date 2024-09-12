<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

class GridEvents
{
    public const string GRID_BUILD_PATTERN = '{application}.{resource}.grid_build';
    public const string GRID_BUILD = 'lag_admin.resource.grid_build';
}
