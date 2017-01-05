<?php

namespace LAG\AdminBundle\Admin\Event;

use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\EventDispatcher\Event;

class AdminCreatedEvent extends Event
{
    /**
     * Admin recently created
     *
     * @var AdminInterface
     */
    protected $admin;

    /**
     * AdminCreatedEvent constructor.
     *
     * @param AdminInterface $admin
     */
    public function __construct(AdminInterface $admin)
    {
        $this->admin = $admin;
    }

    /**
     * @return AdminInterface
     */
    public function getAdmin()
    {
        return $this->admin;
    }
}
