<?php

namespace LAG\AdminBundle\Action\Event;

use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\EventDispatcher\Event;

class ActionCreatedEvent extends Event
{
    /**
     * @var ActionInterface
     */
    protected $action;

    /**
     * @var AdminInterface
     */
    protected $admin;

    /**
     * ActionCreatedEvent constructor.
     *
     * @param ActionInterface $action
     * @param AdminInterface $admin
     */
    public function __construct(ActionInterface $action, AdminInterface $admin)
    {
        $this->action = $action;
        $this->admin = $admin;
    }

    /**
     * @return ActionInterface
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return AdminInterface
     */
    public function getAdmin()
    {
        return $this->admin;
    }
}
