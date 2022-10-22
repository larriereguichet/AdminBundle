<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Metadata\Filter\FilterInterface;

class FilterEvent
{
    public const FILTER_CREATE = 'lag_admin.filter.create';
    public const FILTER_CREATED = 'lag_admin.filter.created';

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
