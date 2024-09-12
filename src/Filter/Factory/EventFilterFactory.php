<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Filter\Factory;

use LAG\AdminBundle\Event\FilterEvent;
use LAG\AdminBundle\Event\FilterEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\FilterInterface;

final readonly class EventFilterFactory implements FilterFactoryInterface
{
    public function __construct(
        private ResourceEventDispatcherInterface $eventDispatcher,
        private FilterFactoryInterface $decorated,
    ) {
    }

    public function create(CollectionOperationInterface $operation, FilterInterface $filter): FilterInterface
    {
        $event = new FilterEvent($filter, $operation);

        $this->eventDispatcher->dispatchEvents(
            $event,
            FilterEvents::FILTER_CREATE_EVENT_PATTERN,
            $operation->getResource()->getApplication(),
            $operation->getResource()->getName(),
            $operation->getName(),
        );

        $filter = $this->decorated->create($operation, $event->getFilter());

        $this->eventDispatcher->dispatchEvents(
            $event,
            FilterEvents::FILTER_CREATED_EVENT_PATTERN,
            $operation->getResource()->getApplication(),
            $operation->getResource()->getName(),
            $operation->getName(),
        );

        return $filter;
    }
}
