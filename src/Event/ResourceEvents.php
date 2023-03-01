<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

class ResourceEvents
{
    public const RESOURCE_CREATE = 'lag_admin.resource.create';
    public const RESOURCE_CREATED = 'lag_admin.resource.created';
    public const RESOURCE_CREATE_PATTERN = 'lag_admin.resource.%s.create';
    public const RESOURCE_CREATED_PATTERN = 'lag_admin.resource.%s.created';
    public const RESOURCE_COLLECTION_LOADED = 'lag_admin.resources.loaded';
}
