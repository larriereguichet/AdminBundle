<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

enum AdminEvents: string
{
    public const ADMIN_REQUEST = 'lag.admin.request';
    public const ADMIN_DATA = 'lag.admin.data';
    public const ADMIN_FILTER = 'lag.admin.data_filter';
    public const ADMIN_ORDER = 'lag.admin.data_order';
    public const ADMIN_FORM = 'lag.admin.form';
    public const ADMIN_HANDLE_FORM = 'lag.admin.handle_form';
    public const ADMIN_VIEW = 'lag.admin.view';
    public const ADMIN_CREATE = 'lag.admin.create';
    public const ADMIN_CONFIGURATION = 'lag.admin.admin_configuration';

    public const ACTION_CREATE = 'lag.admin.action_create';
    public const ACTION_CONFIGURATION = 'lag.admin.action_configuration';

    public const FIELD_CREATE = 'lag.admin.field_create';
    public const FIELD_CONFIGURATION = 'lag.admin.field_configuration';
    public const FIELD_CREATED = 'lag.admin.field_created';
    public const FIELD_DEFINITION_CREATE = 'lag.admin.field_definition_create';
}
// TODO enum
