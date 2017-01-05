<?php

namespace LAG\AdminBundle\Admin\Request\Extractor;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestParametersExtractor
{
    /**
     * Extract parameters for the load method from the Request.
     *
     * @param Request             $request
     * @param ActionConfiguration $configuration
     * @param FormInterface|null  $form
     *
     * @return array
     */
    public function extract(Request $request, ActionConfiguration $configuration, FormInterface $form = null)
    {
        $criteria = $this->getCriteriaValues($request, $configuration);
        $pagination = $this->getPaginationValues($request, $configuration);
        $orders = $this->getOrderValues($request, $configuration);
        $filters = $this->getFilterValues($request, $form);
        $criteria = array_merge($criteria, $filters['filters']);
    
        return [
            'criteria' => $criteria,
            'order' => $orders,
            'maxPerPage' => $pagination['maxPerPage'],
            'page' => $pagination['page'],
            'filterOptions' => $filters['filterOptions'],
        ];
    }
    
    /**
     * Get the criteria values.
     *
     * @param Request             $request
     * @param ActionConfiguration $configuration
     *
     * @return array
     */
    private function getCriteriaValues(Request $request, ActionConfiguration $configuration)
    {
        $values = [];
        $criteriaConfiguration = $configuration->getParameter('criteria');
    
        foreach ($criteriaConfiguration as $criterion) {
            $value = $request->get($criterion);
    
            if (null !== $value) {
                $values[$criterion] = $value;
            }
        }
        
        return $values;
    }
    
    /**
     * Get the sort and order values.
     *
     * @param Request             $request
     * @param ActionConfiguration $configuration
     *
     * @return array
     */
    private function getOrderValues(Request $request, ActionConfiguration $configuration)
    {
        if (true !== $configuration->getParameter('sortable')) {
            return [];
        }
        $values = [];
    
        if (null !== $request->get('sort')) {
            $values['sort'] = $request->get('sort');
        }
    
        if (null !== $request->get('order')) {
            $values['order'] = $request->get('order');
        }
    
        return $values;
    }
    
    /**
     * Get the pagination values.
     *
     * @param Request             $request
     * @param ActionConfiguration $configuration
     *
     * @return array
     */
    private function getPaginationValues(Request $request, ActionConfiguration $configuration)
    {
        $pagination = [
            'page' => null,
            'maxPerPage' => $configuration->getParameter('max_per_page'),
        ];
        // the pagination is required if the load strategy is multiple and the pagerfanta is configured
        $isPaginationRequired = AdminInterface::LOAD_STRATEGY_MULTIPLE === $configuration->getParameter('load_strategy')
            && 'pagerfanta' === $configuration->getParameter('pager');
    
        if ($isPaginationRequired) {
            // retrieve the page parameter value
            $pagination['page'] = $request->get('page', 1);
        }
    
        if (null !== $request->get('maxPerPage')) {
            $pagination['maxPerPage'] = $request->get('maxPerPage');
        }
    
        return $pagination;
    }
    
    /**
     * Handle the value of the filter form.
     *
     * @param Request            $request
     * @param FormInterface|null $form
     *
     * @return array|mixed
     */
    private function getFilterValues(Request $request, FormInterface $form = null)
    {
        // if no filter form was provided, nothing to do
        if (null === $form) {
            return [
                'filters' => [],
                'filterOptions' => [],
            ];
        }
        $filters = [];
        $filtersOptions = [];
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            
            foreach ($form->getData() as $filter => $value) {
    
                if (null === $value) {
                    continue;
                }
                $filters[$filter] = $value;
                $filtersOptions[$filter] = [
                    'operator' => 'LIKE',
                ];
            }
        }
        
        return [
            'filters' => $filters,
            'filterOptions' => $filtersOptions,
        ];
    }
}
