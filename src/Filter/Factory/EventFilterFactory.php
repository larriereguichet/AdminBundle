<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Filter\Factory;

use LAG\AdminBundle\Event\FilterEvent;
use LAG\AdminBundle\Metadata\Filter\FilterInterface;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventFilterFactory implements FilterFactoryInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private FilterFactoryInterface $decorated,
    ) {
    }

    public function create(FilterInterface $filterDefinition): FilterInterface
    {
        $event = new FilterEvent($filterDefinition);
        $this->eventDispatcher->dispatch($event, FilterEvent::FILTER_CREATE);
        $this->eventDispatcher->dispatch($event, sprintf(FilterEvent::FILTER_CREATE_PATTERN, $filterDefinition->getName()));

        $filterDefinition = $this->decorated->create($filterDefinition);

        $event = new FilterEvent($filterDefinition);
        $this->eventDispatcher->dispatch($event, FilterEvent::FILTER_CREATED);
        $this->eventDispatcher->dispatch($event, sprintf(FilterEvent::FILTER_CREATED_PATTERN, $filterDefinition->getName()));

        return $filterDefinition;
    }

    public function createFromProperty(PropertyInterface $property): FilterInterface
    {
        $filter = $this->decorated->createFromProperty($property);

        $event = new FilterEvent($filter);
        $this->eventDispatcher->dispatch($event, FilterEvent::FILTER_CREATED);
        $this->eventDispatcher->dispatch($event, sprintf(FilterEvent::FILTER_CREATED_PATTERN, $filter->getName()));

        return $filter;
    }
}
