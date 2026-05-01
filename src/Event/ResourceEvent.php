<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Metadata\ResourceInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ResourceEvent extends Event implements ResourceEventInterface
{
    public function __construct(private ResourceInterface $resource)
    {
    }

    public function getResource(): ResourceInterface
    {
        return $this->resource;
    }

    public function setResource(ResourceInterface $resource): void
    {
        $this->resource = $resource;
    }
}
