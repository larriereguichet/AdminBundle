<?php

namespace LAG\AdminBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

class RequestFilter implements RequestFilterInterface
{
    /**
     * @var array
     */
    protected $filters;

    /**
     * @var array
     */
    protected $criteria = [];

    /**
     * @var array
     */
    protected $orders;

    /**
     * @var int
     */
    protected $maxPerPage;

    /**
     * @var int
     */
    protected $currentPage;

    /**
     * Configure the filter.
     *
     * @param array $filters Filters for criteria parameters from configuration. Those parameters will be filtered from
     * the request
     * @param array $orders Default orders from configuration
     * @param int $maxPerPage
     */
    public function configure(array $filters = [], array $orders = [], $maxPerPage = 1)
    {
        $this->filters = $filters;
        $this->orders = $orders;
        $this->maxPerPage = $maxPerPage;
    }

    /**
     * Filter request values according to configured filters. Get orders and sort parameters from the request too.
     *
     * @param Request $request
     */
    public function filter(Request $request)
    {
        // filter the request parameters with configured filters for criteria parameters
        $filteredValues = new ParameterBag();

        foreach ($this->filters as $filter) {
            $value = $request->get($filter);

            if ($value !== null) {
                $filteredValues->set($filter, $value);
            }
        }
        $this->criteria = $filteredValues->all();

        // filter the request parameters to find some orders parameters
        if ($request->get('sort') && $request->get('order')) {
            // if sort and order parameters are present in the request, we use them
            $this->orders = [
                $request->get('sort') => $request->get('order')
            ];
        }
    }

    public function getCriteria()
    {
        return $this->criteria;
    }

    public function getOrder()
    {
        return $this->orders;
    }

    public function getMaxPerPage()
    {
        return $this->maxPerPage;
    }

    public function getCurrentPage()
    {
        return 1;
    }
}
