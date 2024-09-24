<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

class OperationEvents
{
    public const string OPERATION_CREATE_PATTERN = '{application}.{resource}.operation_create';
    public const string OPERATION_CREATED_PATTERN = '{application}.{resource}.operation_created';

    public const string OPERATION_CREATE = 'lag_admin.resource.operation_create';
    public const string OPERATION_CREATED = 'lag_admin.resource.operation_created';
}
