<?php

namespace LAG\AdminBundle\Event;

class AdminEvents
{
    const ADMIN_REQUEST = 'lag.admin.request';
    const ADMIN_DATA = 'lag.admin.data';
    const ADMIN_FILTER = 'lag.admin.data_filter';
    const ADMIN_ORDER = 'lag.admin.data_order';
    const ADMIN_FORM = 'lag.admin.form';
    const ADMIN_HANDLE_FORM = 'lag.admin.handle_form';
    const ADMIN_VIEW = 'lag.admin.view';
    const ADMIN_CREATE = 'lag.admin.create';
    const ADMIN_CONFIGURATION = 'lag.admin.admin_configuration';

    const ACTION_CREATE = 'lag.admin.action_create';
    const ACTION_CONFIGURATION = 'lag.admin.action_configuration';

    const FIELD_CREATE = 'lag.admin.field_create';
    const FIELD_CONFIGURATION = 'lag.admin.field_configuration';
    const FIELD_CREATED = 'lag.admin.field_created';
    const FIELD_DEFINITION_CREATE = 'lag.admin.field_definition_create';
}
