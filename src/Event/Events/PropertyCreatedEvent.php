<?php

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Metadata\Property\PropertyInterface;
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
