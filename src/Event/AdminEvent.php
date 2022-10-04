<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Metadata\Admin;
use Symfony\Contracts\EventDispatcher\Event;

class AdminEvent extends Event
{
    public const ADMIN_CREATE = 'lag_admin.admin.create';
    public const ADMIN_CREATED = 'lag_admin.admin.created';

    public function __construct(private Admin $admin)
    {
    }

    public function setAdmin(Admin $admin): void
    {
        $this->admin = $admin;
    }

    public function getAdmin(): Admin
    {
        return $this->admin;
    }
}
