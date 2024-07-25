<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Resource\Metadata\PropertyInterface;
use Symfony\Contracts\EventDispatcher\Event;

class PropertyCreatedEvent extends Event
{
    public function __construct(
        private PropertyInterface $property
    ) {
    }

    public function getProperty(): PropertyInterface
    {
        return $this->property;
    }
}
