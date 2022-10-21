<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Metadata\AdminResource;
use Symfony\Contracts\EventDispatcher\Event;

class ResourceCreatedEvent extends Event
{
    public function __construct(private AdminResource $resource)
    {
    }

    public function getResource(): AdminResource
    {
        return $this->resource;
    }
}
