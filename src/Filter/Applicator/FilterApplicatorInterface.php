<?php

namespace LAG\AdminBundle\Filter\Applicator;

use LAG\AdminBundle\Filter\FilterInterface;

interface FilterApplicatorInterface
{
    /**
     * Return true if the given filter can be applied.
     *
     * @param FilterInterface $filter
     *
     * @return bool
     */
    public function supports(FilterInterface $filter): bool;

    /**
     * Apply the given filter.
     *
     * @param FilterInterface $filter
     */
    public function apply(FilterInterface $filter): void;
}
