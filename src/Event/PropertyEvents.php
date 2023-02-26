<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

class PropertyEvents
{
    public const PROPERTY_CREATE = 'lag_admin.property.create';
    public const PROPERTY_CREATED = 'lag_admin.property.created';
    public const PROPERTY_CREATE_PATTERN = 'lag_admin.property.%s.create';
    public const PROPERTY_CREATED_PATTERN = 'lag_admin.property.%s.created';
}
