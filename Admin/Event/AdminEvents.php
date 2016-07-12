<?php

namespace LAG\AdminBundle\Admin\Event;

class AdminEvents
{
    /**
     * Dispatched before Admins creation.
     */
    const BEFORE_CONFIGURATION = 'lag.admin.beforeConfigurationLoad';

    /**
     * Event dispatched before the creation of an Admin
     */
    const ADMIN_CREATE = 'lag.event.admin.create';

    /**
     * Event dispatched after the creation of an Admin
     */
    const ADMIN_CREATED = 'lag.event.admin.created';

    /**
     * Event dispatched before the creation of an Action
     */
    const ACTION_CREATE = 'lag.event.action.create';

    /**
     * Event dispatched after the creation of an Action
     */
    const ACTION_CREATED = 'lag.event.action.created';
}
