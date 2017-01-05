<?php

namespace LAG\AdminBundle\Admin\Request;

use Exception;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\HttpFoundation\Request;

class LoadParameterExtractor
{
    /**
     * @var ActionConfiguration
     */
    private $configuration;
    
    /**
     * @var array
     */
    private $criteria = [];
    
    /**
     * @var array
     */
    private $order = [];
    
    /**
     * @var int
     */
    private $maxPerPage = 5000;
    
    /**
     * @var int
     */
    private $page = 1;
    
    /**
     * LoadParameterExtractor constructor.
     *
     * @param ActionConfiguration $configuration
     * @param array               $filters
     *
     * @throws Exception
     */
    public function __construct(ActionConfiguration $configuration, array $filters = [])
    {
        $this->configuration = $configuration;
    
        $configuredFilters = $this->configuration->getParameter('filters');
        
        foreach ($filters as $filter => $value) {
    
            if (!key_exists($filter, $configuredFilters)) {
                throw new Exception('The filter "'.$filter.'" is not configured');
            }
            
            if (null !== $value) {
                dump($configuredFilters);
                dump($filter);
                if ('string' === $configuredFilters[$filter]['type']) {
                    $value = '%'.$value.'%';
                }
                $this->criteria[$filter] = $value;
            }
        }
    }
    
    /**
     * @param Request $request
     */
    public function load(Request $request)
    {
        $this->loadCriteria($request);
        $this->loadOrder($request);
        $this->loadPagination($request);
    }
    
    /**
     * Get the criteria values.
     *
     * @param Request $request
     */
    private function loadCriteria(Request $request)
    {
        $criteriaConfiguration = $this
            ->configuration
            ->getParameter('criteria')
        ;
        
        foreach ($criteriaConfiguration as $criterion) {
            $value = $request->get($criterion);
            
            if (null !== $value) {
                $this->criteria[$criterion] = $value;
            }
        }
    }
    
    /**
     * @param Request $request
     */
    private function loadOrder(Request $request)
    {
        $sortable = $this
            ->configuration
            ->getParameter('sortable')
        ;
        
        // if the Action is not sortable, we do not load order parameters
        if (true !== $sortable) {
            return;
        }
        $this->order = $this->configuration->getParameter('order');
        $sort = $request->get('sort');
        $order = $request->get('order', 'ASC');
    
        if ($sort) {
            $this->order = [
                $sort => $order,
            ];
        }
    
    }
    
    /**
     * Get the pagination values.
     *
     * @param Request             $request
     */
    private function loadPagination(Request $request)
    {
        // the default value is the configured one
        $this->maxPerPage = $this->configuration->getParameter('max_per_page');
        
        if (false === $this->configuration->getParameter('pager')) {
            return;
        }
        // the pagination is required if the load strategy is multiple and the pagerfanta is configured
        $isPaginationRequired =
            AdminInterface::LOAD_STRATEGY_MULTIPLE === $this->configuration->getParameter('load_strategy') &&
            'pagerfanta' === $this->configuration->getParameter('pager')
        ;
        
        if ($isPaginationRequired) {
            // retrieve the page parameter value
            $this->page = (int)$request->get('page', 1);
        }
        
        if (null !== $request->get('maxPerPage')) {
            $this->maxPerPage = $request->get('maxPerPage');
        }
    }
    
    /**
     * @return array
     */
    public function getCriteria()
    {
        return $this->criteria;
    }
    
    /**
     * @return array
     */
    public function getOrder()
    {
        return $this->order;
    }
    
    /**
     * @return int
     */
    public function getMaxPerPage()
    {
        return $this->maxPerPage;
    }
    
    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }
}
