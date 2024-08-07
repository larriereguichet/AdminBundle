<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Resource\Metadata\Resource;
use Symfony\Contracts\EventDispatcher\Event;

class ResourceEvent extends Event
{
    public function __construct(private Resource $resource)
    {
    }

    public function getResource(): Resource
    {
        return $this->resource;
    }

    public function setResource(Resource $resource): void
    {
        $this->resource = $resource;
    }
}
