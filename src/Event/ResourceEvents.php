<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

class ResourceEvents
{
    public const ADMIN_CREATE = 'lag_admin.resource.create';
    public const ADMIN_CREATED = 'lag_admin.resource.created';
    public const ADMIN_CREATE_PATTERN = 'lag_admin.resource.%s.create';
    public const ADMIN_CREATED_PATTERN = 'lag_admin.resource.%s.created';
    public const RESOURCE_COLLECTION_LOADED = 'lag_admin.resources.loaded';
}
