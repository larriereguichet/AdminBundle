<?php

namespace LAG\AdminBundle\Filter;

use Symfony\Component\HttpFoundation\Request;

interface RequestFilterInterface
{
    public function configure(array $filters, array $orders, $maxPerPage = 1);

    public function filter(Request $request);

    public function getCriteria();

    public function getOrder();

    public function getMaxPerPage();

    public function getCurrentPage();
}
