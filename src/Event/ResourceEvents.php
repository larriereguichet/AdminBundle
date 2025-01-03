<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

class ResourceEvents
{
    public const string RESOURCE_CREATE_PATTERN = '{application}.{resource}.create';
    public const string RESOURCE_CREATED_PATTERN = '{application}.{resource}.created';

    public const string RESOURCE_CREATE = 'lag_admin.resource.create';
    public const string RESOURCE_CREATED = 'lag_admin.resource.created';
}
