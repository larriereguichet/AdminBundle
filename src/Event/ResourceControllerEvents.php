<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

class ResourceControllerEvents
{
    public const string RESOURCE_CONTROLLER_PATTERN = '{application}.{resource}.controller';
    public const string RESOURCE_CONTROLLER = 'lag_admin.resource.controller';
}
