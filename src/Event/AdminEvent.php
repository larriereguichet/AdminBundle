<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Metadata\Admin;
use Symfony\Contracts\EventDispatcher\Event;

class AdminEvent extends Event
{
    public const ADMIN_CREATE = 'lag_admin.admin.create';
    public const ADMIN_CREATED = 'lag_admin.admin.created';

    public function __construct(private Admin $resource)
    {
    }

    public function setResource(Admin $resource): void
    {
        $this->resource = $resource;
    }

    public function getResource(): Admin
    {
        return $this->resource;
    }
}
