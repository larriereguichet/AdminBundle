<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

enum OperationEvents: string
{
    public const OPERATION_CREATE = 'lag_admin.operation.create';
    public const NAMED_RESOURCE_CREATE = 'lag_admin.{resource}.operation.create';
    public const NAMED_RESOURCE_OPERATION_CREATE = 'lag_admin.{resource}.operation.{operation}.create';

    public const OPERATION_CREATED = 'lag_admin.operation.created';
    public const NAMED_RESOURCE_CREATED = 'lag_admin.{resource}.operation.created';
    public const NAMED_RESOURCE_OPERATION_CREATED = 'lag_admin.{resource}.operation.{operation}.created';
}
