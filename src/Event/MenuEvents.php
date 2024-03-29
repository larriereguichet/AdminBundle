<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

class MenuEvents
{
    public const MENU_CREATE = 'lag_admin.menu.create';
    public const MENU_CREATED = 'lag_admin.menu.created';

    public const PRE_EVENT_PATTERN = 'lag_admin.menu.%s.create';
    public const POST_EVENT_PATTERN = 'lag_admin.menu.%s.created';
}
