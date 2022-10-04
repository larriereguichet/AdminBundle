<?php

namespace LAG\AdminBundle\Admin\Factory;

use LAG\AdminBundle\Event\OperationEvent;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Property\Factory\PropertyFactoryInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OperationFactory implements OperationFactoryInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private ValidatorInterface $validator,
        private PropertyFactoryInterface $propertyFactory,
    )
    {
    }

    public function create(OperationInterface $operation): OperationInterface
    {
        $this->eventDispatcher->dispatch($event = new OperationEvent($operation), OperationEvent::OPERATION_CREATE);
        $errors = $this->validator->validate($event->getOperation(), [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidOperationException($operation->getName(), $errors);
        }
        $properties = [];

        foreach ($operation->getProperties() as $property) {
            $properties[] = $this->propertyFactory->create($property);
        }
        $operation = $operation->withProperties($properties);
        $this->eventDispatcher->dispatch($event = new OperationEvent($operation), OperationEvent::OPERATION_CREATED);

        return $event->getOperation();
    }
}
