<?php

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class AdminEvent extends Event
{
    /**
     * @var AdminInterface
     */
    private $admin;

    /**
     * @var ActionInterface
     */
    private $action;

    /**
     * @var Request
     */
    private $request;

    /**
     * AdminEvent constructor.
     *
     * @param AdminInterface   $admin
     * @param Request $request
     */
    public function __construct(AdminInterface $admin, Request $request)
    {
        $this->admin = $admin;
        $this->request = $request;
    }

    /**
     * @return AdminInterface
     */
    public function getAdmin(): AdminInterface
    {
        return $this->admin;
    }

    /**
     * @return ActionInterface
     */
    public function getAction(): ActionInterface
    {
        return $this->action;
    }

    /**
     * @return bool
     */
    public function hasAction(): bool
    {
        return null !== $this->action;
    }

    /**
     * @param ActionInterface $action
     */
    public function setAction(ActionInterface $action)
    {
        $this->action = $action;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
