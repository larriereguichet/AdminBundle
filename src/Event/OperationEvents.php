<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

enum OperationEvents: string
{
    public const OPERATION_CREATE = 'operation.create';
    public const OPERATION_CREATED = 'operation.created';
}
