<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

class DataEvents
{
    public const DATA_PROCESS = 'lag_admin.data.process';
    public const DATA_PROCESSED = 'lag_admin.data.processed';

    public const DATA_RESOURCE_PROCESS = 'lag_admin.{resource}.data.process';
    public const DATA_RESOURCE_PROCESSED = 'lag_admin.{resource}.data.processed';

    public const DATA_OPERATION_PROCESS = 'lag_admin.{resource}.{operation}.data.process';
    public const DATA_OPERATION_PROCESSED = 'lag_admin.{resource}.{operation}.data.processed';
}
