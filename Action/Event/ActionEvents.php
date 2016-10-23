<?php

namespace LAG\AdminBundle\Action\Event;

class ActionEvents
{
    /**
     * Dispatched before Actions creation.
     */
    const BEFORE_CONFIGURATION = 'lag.event.action.beforeConfigurationLoad';

    /**
     * Event dispatched before the creation of an Action
     */
    const ACTION_CREATE = 'lag.event.action.create';

    /**
     * Event dispatched after the creation of an Action
     */
    const ACTION_CREATED = 'lag.event.action.created';
}
