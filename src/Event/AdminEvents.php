<?php

namespace LAG\AdminBundle\Event;

class AdminEvents
{
    const HANDLE_REQUEST = 'lag.admin.handleRequest';

    const HANDLE_FORM = 'lag.admin.handleForm';

    const ACTION_CONFIGURATION = 'lag.admin.action_configuration';

    const VIEW = 'lag.admin.view';

    const MENU = 'lag.admin.menu';

    const ENTITY = 'lag.admin.entity';

    const DOCTRINE_ORM_FILTER = 'lag.admin.doctrine_orm_filter';
}
