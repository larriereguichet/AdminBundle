<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

enum ResourceEvents: string
{
    public const ADMIN_CREATE = 'lag_admin.admin.create';
    public const ADMIN_CREATED = 'lag_admin.admin.created';
}
