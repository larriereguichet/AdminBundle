<?php

namespace LAG\AdminBundle\EventListener\Property;

use LAG\AdminBundle\Event\Events\PropertyEvent;
use function Symfony\Component\String\u;

class CreateListener
{
    public function __invoke(PropertyEvent $event): void
    {
        $property = $event->getProperty();

        if (!$property->getPropertyPath()) {
            $property = $property->withPropertyPath($property->getName());
        }

        if (!$property->getLabel()) {
            $property = $property->withLabel(u($property->getName())->title());
        }

        $event->setProperty($property);
    }
}
