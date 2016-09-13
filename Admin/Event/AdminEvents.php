<?php

namespace LAG\AdminBundle\Admin\Event;

class AdminEvents
{
    /**
     * Dispatched before Admins creation.
     */
    const BEFORE_CONFIGURATION = 'lag.event.admin.beforeConfigurationLoad';

    /**
     * Event dispatched before the creation of an Admin
     */
    const ADMIN_CREATE = 'lag.event.admin.create';

    /**
     * Event dispatched after the creation of an Admin
     */
    const ADMIN_CREATED = 'lag.event.admin.created';
}
