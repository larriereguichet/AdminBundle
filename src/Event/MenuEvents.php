<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

class MenuEvents
{
    public const MENU_CONFIGURATION = 'lag.admin.menu_configuration';
    public const MENU_CONFIGURATION_SPECIFIC = 'lag.admin.menu_%s_configuration';

    public const MENU_CREATE = 'lag.admin.menu_create';
    public const MENU_CREATE_SPECIFIC = 'lag.admin.menu_%s_create';

    public const MENU_CREATED = 'lag.admin.menu_created';
    public const MENU_CREATED_SPECIFIC = 'lag.admin.menu_%s_created';
}
