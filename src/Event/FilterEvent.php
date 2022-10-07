<?php

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Metadata\Filter\FilterInterface;

class FilterEvent
{
    const FILTER_CREATE = 'lag_admin.filter.create';
    const FILTER_CREATED = 'lag_admin.filter.created';

    public function __construct(
        private FilterInterface $filter,
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
}
