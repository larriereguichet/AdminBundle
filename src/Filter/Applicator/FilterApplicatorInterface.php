<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Filter\Applicator;

use LAG\AdminBundle\Filter\FilterInterface;

interface FilterApplicatorInterface
{
    /**
     * Return true if the given filter can be applied.
     */
    public function supports(FilterInterface $filter): bool;

    /**
     * Apply the given filter.
     */
    public function apply(FilterInterface $filter): void;
}
