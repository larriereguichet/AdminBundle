<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

class FilterEvents
{
    public const string FILTER_CREATE_EVENT_PATTERN = '{application}.{resource}.filter_create';
    public const string FILTER_CREATED_EVENT_PATTERN = '{application}.{resource}.filter_created';

    public const string FILTER_CREATE = 'lag_admin.resource.filter_create';
    public const string FILTER_CREATED = 'lag_admin.resource.filter_created';
}
