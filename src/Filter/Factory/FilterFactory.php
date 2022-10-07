<?php

namespace LAG\AdminBundle\Filter\Factory;

use LAG\AdminBundle\Event\FilterEvent;
use LAG\AdminBundle\Exception\Validation\InvalidFilterException;
use LAG\AdminBundle\Metadata\Filter\Filter;
use LAG\AdminBundle\Metadata\Filter\FilterInterface;
use LAG\AdminBundle\Metadata\Filter\StringFilter;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;
use LAG\AdminBundle\Metadata\Property\StringProperty;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FilterFactory implements FilterFactoryInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function create(FilterInterface $filter): FilterInterface
    {
        $this->eventDispatcher->dispatch($event = new FilterEvent($filter), FilterEvent::FILTER_CREATE);
        $errors = $this->validator->validate($event->getFilter(), [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidFilterException($filter->getName(), $errors);
        }
        $filter = $event->getFilter();
        $this->eventDispatcher->dispatch(new FilterEvent($filter), FilterEvent::FILTER_CREATED);

        return $filter;
    }

    public function createFromProperty(PropertyInterface $property): FilterInterface
    {
        $class = Filter::class;
        $name = $property->getName();

        if ($property instanceof StringProperty) {
            $class = StringFilter::class;
        }

        return new $class(
            name: $name,
            propertyPath: $property->getPropertyPath(),
        );
    }
}
