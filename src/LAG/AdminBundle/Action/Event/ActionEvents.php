<?php

namespace LAG\AdminBundle\Action\Event;

class ActionEvents
{
    /**
     * Dispatched before Actions creation.
     */
    const BEFORE_CONFIGURATION = 'lag.event.action.beforeConfigurationLoad';

    /**
     * Event dispatched after the creation of an Action
     */
    const ACTION_CREATED = 'lag.event.action.created';
}
