<?php

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractEvent extends Event
{
    /**
     * @var AdminInterface
     */
    protected $admin;

    /**
     * @var Request
     */
    protected $request;

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
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
