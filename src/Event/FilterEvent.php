<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\FilterInterface;
use Symfony\Contracts\EventDispatcher\Event;

class FilterEvent extends Event
{
    public function __construct(
        private FilterInterface $filter,
        private readonly CollectionOperationInterface $operation,
    ) {
    }

    public function getFilter(): FilterInterface
    {
        return $this->filter;
    }

    public function setFilter(FilterInterface $filter): void
    {
        $this->filter = $filter;
    }

    public function getOperation(): CollectionOperationInterface
    {
        return $this->operation;
    }
}
