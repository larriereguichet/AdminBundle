<?php

namespace LAG\AdminBundle\Property\Factory;

use LAG\AdminBundle\Event\Events\PropertyEvent;
use LAG\AdminBundle\Event\PropertyEvents;
use LAG\AdminBundle\Exception\Validation\InvalidPropertyException;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PropertyFactory implements PropertyFactoryInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private ValidatorInterface $validator,
    )
    {
    }

    public function create(PropertyInterface $property): PropertyInterface
    {
        $event = new PropertyEvent($property);
        $this->eventDispatcher->dispatch($event, PropertyEvents::PROPERTY_CREATE->value);

        $errors = $this->validator->validate($event->getProperty(), [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidPropertyException($property->getName(), $errors);
        }
        $event = new PropertyEvent($property);
        $this->eventDispatcher->dispatch($event, PropertyEvents::PROPERTY_CREATE->value);

        return $event->getProperty();
    }
}
