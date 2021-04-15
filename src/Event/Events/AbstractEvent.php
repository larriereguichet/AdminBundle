<?php

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class AbstractEvent extends Event
{
    private AdminInterface $admin;
    private Request $request;

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
