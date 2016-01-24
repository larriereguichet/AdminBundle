<?php

namespace LAG\AdminBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

class PagerfantaFilter
{
    /**
     * Filter request
     *
     * @param Request $request
     * @return ParameterBag
     */
    public function filter(Request $request)
    {
        $filteredValues = new ParameterBag();
        // order column, like "name"
        $order = $request->get('order');
        // sort value, asc or desc
        $sort = $request->get('sort');
        // page number, like 2
        $page = $request->get('page');

        if ($order) {
            if (!$sort) {
                $sort = 'asc';
            }
            $filteredValues->set('order', [
                $order => $sort
            ]);
        }
        if ($page) {
            $filteredValues->set('page', $page);
        }
        return $filteredValues;
    }
}
