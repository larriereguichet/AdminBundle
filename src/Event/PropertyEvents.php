<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

enum PropertyEvents: string
{
    case PROPERTY_CREATE = 'lag_admin.property.create';
    case PROPERTY_CREATED = 'lag_admin.property.created';
}
