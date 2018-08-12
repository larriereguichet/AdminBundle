<?php

namespace LAG\AdminBundle\Event;

class AdminEvents
{
    const HANDLE_REQUEST = 'lag.admin.handleRequest';

    const HANDLE_FORM = 'lag.admin.handleForm';

    const ADMIN_CONFIGURATION = 'lag.admin.admin_configuration';

    const ACTION_CONFIGURATION = 'lag.admin.action_configuration';

    const MENU_CONFIGURATION = 'lag.admin.menu_configuration';

    const FILTER = 'lag.admin.filter';

    const VIEW = 'lag.admin.view';

    const MENU = 'lag.admin.menu';

    const ENTITY_LOAD = 'lag.admin.entity_load';

    const ENTITY_SAVE = 'lag.admin.entity_save';

    const DOCTRINE_ORM_FILTER = 'lag.admin.doctrine_orm_filter';
}
