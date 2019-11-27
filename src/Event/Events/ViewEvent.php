<?php

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class ViewEvent extends Event
{
    /**
     * @var ViewInterface
     */
    private $view;

    /**
     * @var AdminInterface
     */
    private $admin;

    /**
     * @var Request
     */
    private $request;

    /**
     * ViewEvent constructor.
     */
    public function __construct(AdminInterface $admin, Request $request)
    {
        $this->admin = $admin;
        $this->request = $request;
    }

    public function getView(): ViewInterface
    {
        return $this->view;
    }

    public function setView(ViewInterface $view)
    {
        $this->view = $view;
    }

    public function getAdmin(): AdminInterface
    {
        return $this->admin;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
