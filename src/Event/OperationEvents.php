<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

enum OperationEvents: string
{
    public const OPERATION_CREATE = 'lag_admin.operation.create';
    public const OPERATION_CREATED = 'lag_admin.operation.created';

    public const RESOURCE_OPERATION_CREATE_PATTERN = 'lag_admin.%s.operation.%s.create';
    public const RESOURCE_OPERATION_CREATED_PATTERN = 'lag_admin.%s.operation.%s.created';

    public const OPERATION_CREATE_PATTERN = 'lag_admin.%s.operation.create';
    public const OPERATION_CREATED_PATTERN = 'lag_admin.%s.operation.created';
}
