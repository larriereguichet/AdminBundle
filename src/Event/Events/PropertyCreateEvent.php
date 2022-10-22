<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Metadata\Property\PropertyInterface;
use Symfony\Contracts\EventDispatcher\Event;

class PropertyCreateEvent extends Event
{
    public function __construct(
        private PropertyInterface $property
    ) {
    }

    public function getProperty(): PropertyInterface
    {
        return $this->property;
    }

    public function setProperty(PropertyInterface $property): void
    {
        $this->property = $property;
    }
}
