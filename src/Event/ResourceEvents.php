<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

class ResourceEvents
{
    public const RESOURCE_CREATE = 'lag_admin.resource.create';
    public const RESOURCE_CREATED = 'lag_admin.resource.created';
    public const NAMED_RESOURCE_CREATE = 'lag_admin.resource.{resource}.create';
    public const NAMED_RESOURCE_CREATED = 'lag_admin.resource.{resource}.created';
}
