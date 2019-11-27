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
     */
    public function __construct(AdminInterface $admin, Request $request)
    {
        $this->admin = $admin;
        $this->request = $request;
    }

    public function getAdmin(): AdminInterface
    {
        return $this->admin;
    }

    public function getAction(): ActionInterface
    {
        return $this->action;
    }

    public function hasAction(): bool
    {
        return null !== $this->action;
    }

    public function setAction(ActionInterface $action)
    {
        $this->action = $action;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
