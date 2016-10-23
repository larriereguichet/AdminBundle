<?php

namespace LAG\AdminBundle\Filter\Factory;

use LAG\AdminBundle\Configuration\Configuration;
use LAG\AdminBundle\Filter\PagerfantaFilter;
use LAG\AdminBundle\Filter\RequestFilter;

/**
 * Create request filters according to the given configuration
 */
class RequestFilterFactory
{
    /**
     * Create request filters according to the given configuration.
     *
     * @param Configuration $configuration
     * @return PagerfantaFilter|RequestFilter
     */
    public function create(Configuration $configuration)
    {
        if ($configuration->hasParameter('pager') && $configuration->getParameter('pager') === 'pagerfanta') {
            // if Pagerfanta is configured, use the PagerfantaFilter
            $requestFilter = new PagerfantaFilter();
        } else {
            // else use the classic request filter
            $requestFilter = new RequestFilter();
        }

        return $requestFilter;
    }
}
