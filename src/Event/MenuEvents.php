<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

class MenuEvents
{
    public const MENU_CREATED = 'lag.admin.menu_created';
    public const MENU_CREATED_SPECIFIC = 'lag.admin.menu_%s_created';
}
