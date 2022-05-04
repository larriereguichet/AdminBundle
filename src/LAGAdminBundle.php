<?php

declare(strict_types=1);

namespace LAG\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class LAGAdminBundle extends Bundle
{
    // CRUD Actions
    public const SERVICE_ID_LIST_ACTION = 'lag.admin.actions.list';
    public const SERVICE_ID_CREATE_ACTION = 'lag.admin.actions.create';
    public const SERVICE_ID_EDIT_ACTION = 'lag.admin.actions.edit';
    public const SERVICE_ID_DELETE_ACTION = 'lag.admin.actions.delete';

    public const SERVICE_ID_ACTION_FACTORY = 'lag.admin.action_factory';

    // Responders
    public const SERVICE_ID_LIST_RESPONDER = 'lag.admin.action.list_responder';

    // Form Handlers
    public const SERVICE_ID_EDIT_FORM_HANDLER = 'lag.admin.form.edit_form_handler';
    public const SERVICE_ID_LIST_FORM_HANDLER = 'lag.admin.form.list_form_handler';

    // Service Tags
    public const SERVICE_TAG_FORM_HANDLER = 'lag.admin.form_handler';

    // Request Admin parameters
    // TODO from configuration
    public const REQUEST_PARAMETER_ADMIN = '_admin';
    public const REQUEST_PARAMETER_ACTION = '_action';

    public function getPath(): string
    {
        return __DIR__.'/../';
    }
}
