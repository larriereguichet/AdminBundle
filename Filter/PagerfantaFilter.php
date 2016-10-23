<?php

namespace LAG\AdminBundle\Filter;

use Symfony\Component\HttpFoundation\Request;

class PagerfantaFilter extends RequestFilter
{
    /**
     * The current page that should be retrieved by the pager
     *
     * @var int
     */
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
        // filter normal request parameters
        parent::filter($request);
    }

    /**
     * Return the current page that should be retrieved by the pager
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }
}
