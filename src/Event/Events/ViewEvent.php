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
     *
     * @param AdminInterface $admin
     * @param Request        $request
     */
    public function __construct(AdminInterface $admin, Request $request)
    {
        $this->admin = $admin;
        $this->request = $request;
    }

    /**
     * @return ViewInterface
     */
    public function getView(): ViewInterface
    {
        return $this->view;
    }

    /**
     * @param ViewInterface $view
     */
    public function setView(ViewInterface $view)
    {
        $this->view = $view;
    }

    /**
     * @return AdminInterface
     */
    public function getAdmin(): AdminInterface
    {
        return $this->admin;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
