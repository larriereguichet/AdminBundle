<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

class DataEvents
{
    public const string DATA_PROCESS_EVENT_PATTERN = '{application}.{resource}.data_process';
    public const string DATA_PROCESSED_EVENT_PATTERN = '{application}.{resource}.data_processed';

    public const string DATA_PROCESS = 'lag_admin.resource.data_process';
    public const string DATA_PROCESSED = 'lag_admin.resource.data_processed';
}
