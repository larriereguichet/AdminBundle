<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Property;

use LAG\AdminBundle\Event\Events\PropertyCreateEvent;

use function Symfony\Component\String\u;

class CreateListener
{
    public function __invoke(PropertyCreateEvent $event): void
    {
        $property = $event->getProperty();

        if (!$property->getPropertyPath()) {
            $property = $property->withPropertyPath($property->getName());
        }

        if (!$property->getLabel()) {
            $property = $property->withLabel(u($property->getName())->title()->toString());
        }

        $event->setProperty($property);
    }
}
