<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Filter\Factory;

use LAG\AdminBundle\Event\FilterEvent;
use LAG\AdminBundle\Resource\Metadata\FilterInterface;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventFilterFactory implements FilterFactoryInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private FilterFactoryInterface $decorated,
    ) {
    }

    public function create(FilterInterface $filter): FilterInterface
    {
        $event = new FilterEvent($filter);
        $this->eventDispatcher->dispatch($event, FilterEvent::FILTER_CREATE);
        $this->eventDispatcher->dispatch($event, \sprintf(FilterEvent::FILTER_CREATE_PATTERN, $filter->getName()));

        $filter = $this->decorated->create($filter);

        $event = new FilterEvent($filter);
        $this->eventDispatcher->dispatch($event, FilterEvent::FILTER_CREATED);
        $this->eventDispatcher->dispatch($event, \sprintf(FilterEvent::FILTER_CREATED_PATTERN, $filter->getName()));

        return $filter;
    }

    public function createFromProperty(PropertyInterface $property): FilterInterface
    {
        $filter = $this->decorated->createFromProperty($property);

        $event = new FilterEvent($filter);
        $this->eventDispatcher->dispatch($event, FilterEvent::FILTER_CREATED);
        $this->eventDispatcher->dispatch($event, \sprintf(FilterEvent::FILTER_CREATED_PATTERN, $filter->getName()));

        return $filter;
    }
}
