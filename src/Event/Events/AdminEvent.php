<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Contracts\EventDispatcher\Event;

class AdminEvent extends Event
{
    private AdminInterface $admin;

    public function __construct(AdminInterface $admin)
    {
        $this->admin = $admin;
    }

    public function getAdmin(): AdminInterface
    {
        return $this->admin;
    }
}
