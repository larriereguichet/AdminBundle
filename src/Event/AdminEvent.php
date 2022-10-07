<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Metadata\AdminResource;
use Symfony\Contracts\EventDispatcher\Event;

class AdminEvent extends Event
{
    public const ADMIN_CREATE = 'lag_admin.admin.create';
    public const ADMIN_CREATED = 'lag_admin.admin.created';

    public function __construct(private AdminResource $resource)
    {
    }

    public function setResource(AdminResource $resource): void
    {
        $this->resource = $resource;
    }

    public function getResource(): AdminResource
    {
        return $this->resource;
    }
}
