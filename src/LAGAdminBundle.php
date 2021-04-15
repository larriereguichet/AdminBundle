<?php

namespace LAG\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class LAGAdminBundle extends Bundle
{
    // CRUD Actions
    const SERVICE_ID_LIST_ACTION = 'lag.admin.actions.list';
    const SERVICE_ID_CREATE_ACTION = 'lag.admin.actions.create';
    const SERVICE_ID_EDIT_ACTION = 'lag.admin.actions.edit';
    const SERVICE_ID_DELETE_ACTION = 'lag.admin.actions.delete';

    const SERVICE_ID_ACTION_FACTORY = 'lag.admin.action_factory';

    // Responders
    const SERVICE_ID_LIST_RESPONDER = 'lag.admin.action.list_responder';

    // Form Handlers
    const SERVICE_ID_EDIT_FORM_HANDLER = 'lag.admin.form.edit_form_handler';
    const SERVICE_ID_LIST_FORM_HANDLER = 'lag.admin.form.list_form_handler';

    // Service Tags
    const SERVICE_TAG_FORM_HANDLER = 'lag.admin.form_handler';

    // Request Admin parameters
    // TODO from configuration
    const REQUEST_PARAMETER_ADMIN = '_admin';
    const REQUEST_PARAMETER_ACTION = '_action';
}
