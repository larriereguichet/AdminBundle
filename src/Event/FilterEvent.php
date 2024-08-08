<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Resource\Metadata\FilterInterface;
use Symfony\Contracts\EventDispatcher\Event;

class FilterEvent extends Event
{
    public const FILTER_CREATE = 'lag_admin.filter.create';
    public const FILTER_CREATED = 'lag_admin.filter.created';
    public const FILTER_CREATE_PATTERN = 'lag_admin.filter.%s.create';
    public const FILTER_CREATED_PATTERN = 'lag_admin.filter.%s.created';

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
