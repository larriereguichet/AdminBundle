<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Event\Events\PropertyCreatedEvent;
use LAG\AdminBundle\Event\Events\PropertyCreateEvent;
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
    ) {
    }

    public function create(PropertyInterface $property): PropertyInterface
    {
        $event = new PropertyCreateEvent($property);
        $this->eventDispatcher->dispatch($event, PropertyEvents::PROPERTY_CREATE->value);
        $property = $event->getProperty();

        $errors = $this->validator->validate($property, [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidPropertyException($property->getName(), $errors);
        }
        $this->eventDispatcher->dispatch(new PropertyCreatedEvent($property), PropertyEvents::PROPERTY_CREATED->value);

        return $property;
    }
}
