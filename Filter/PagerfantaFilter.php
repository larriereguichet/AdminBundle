<?php

namespace LAG\AdminBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

class PagerfantaFilter extends RequestFilter
{
    protected $currentPage;

    /**
     * Filter request
     *
     * @param Request $request
     */
    public function filter(Request $request)
    {
        if ($request->get('page')) {
            $this->currentPage = $request->get('page');
        }
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }
}
