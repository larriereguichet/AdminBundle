<?php

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

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

    public function getRequest(): Request
    {
        return $this->request;
    }
}
