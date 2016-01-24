<?php

namespace LAG\AdminBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

class RequestFilter
{
    /**
     * @var array
     */
    protected $filters;

    /**
     * RequestFilter constructor.
     *
     * @param array $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Filter request values according to configured filters
     *
     * @param Request $request
     * @return ParameterBag
     */
    public function filter(Request $request)
    {
        $filteredValues = new ParameterBag();

        foreach ($this->filters as $filter) {
            $value = $request->get($filter);

            if ($value !== null) {
                $filteredValues->set($filter, $value);
            }
        }
        return $filteredValues;
    }
}
