<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Event\Events\OperationCreatedEvent;
use LAG\AdminBundle\Event\Events\OperationCreateEvent;
use LAG\AdminBundle\Event\OperationEvents;
use LAG\AdminBundle\Exception\Validation\InvalidOperationException;
use LAG\AdminBundle\Filter\Factory\FilterFactoryInterface;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OperationFactory implements OperationFactoryInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private ValidatorInterface $validator,
        private PropertyFactoryInterface $propertyFactory,
        private FilterFactoryInterface $filterFactory,
    ) {
    }

    public function create(AdminResource $resource, OperationInterface $operation): OperationInterface
    {
        $operation = $operation
            ->withResource($resource)
            ->withResourceName($resource->getName())
        ;
        $this->eventDispatcher->dispatch($event = new OperationCreateEvent($operation), OperationEvents::OPERATION_CREATE);
        $errors = $this->validator->validate($operation = $event->getOperation(), [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidOperationException($operation->getName(), $errors);
        }
        $properties = [];

        foreach ($operation->getProperties() as $property) {
            $properties[] = $this->propertyFactory->create($property);
        }
        $operation = $operation->withProperties($properties);

        if ($operation instanceof CollectionOperationInterface) {
            $filters = [];

            foreach ($operation->getFilters() as $filter) {
                $filters[] = $this->filterFactory->create($filter);
            }
            $operation = $operation->withFilters($filters);
        }

        $this->eventDispatcher->dispatch(new OperationCreatedEvent($operation), OperationEvents::OPERATION_CREATED);

        return $operation;
    }
}
