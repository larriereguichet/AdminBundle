<?php

namespace LAG\AdminBundle\Event;

class Events
{
    const ADMIN_HANDLE_REQUEST = 'lag.admin.handleRequest';
    const ADMIN_CREATE_FORM = 'lag.admin.createForm';
    const ADMIN_HANDLE_FORM = 'lag.admin.handleForm';
    const ADMIN_FILTER = 'lag.admin.filter';
    const ADMIN_VIEW = 'lag.admin.view';

    const MENU = 'lag.admin.menu';
    const MENU_CREATE = 'lag.admin.menu_create';
    const MENU_CREATED = 'lag.admin.menu_created';

    const CONFIGURATION_ADMIN = 'lag.configuration.admin';
    const CONFIGURATION_ACTION = 'lag.configuration.action';

    const MENU_CONFIGURATION = 'lag.admin.menu_configuration';

    const ENTITY_LOAD = 'lag.admin.entity_load';
    const ENTITY_SAVE = 'lag.admin.entity_save';
    const DOCTRINE_ORM_FILTER = 'lag.admin.doctrine_orm_filter';

    const FIELD_PRE_CREATE = 'lag.field.pre_create';
    const FIELD_POST_CREATE = 'lag.field.post_create';

    const FORM_PRE_CREATE_ENTITY_FORM = 'lag.form.pre_create_entity_form';
}
