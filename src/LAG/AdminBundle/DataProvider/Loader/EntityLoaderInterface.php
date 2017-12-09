<?php

namespace LAG\AdminBundle\DataProvider\Loader;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use Pagerfanta\Pagerfanta;

interface EntityLoaderInterface
{
    /**
     * Load the entities according to given criteria. An optional pagination can also be used.
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int   $limit
     * @param int   $offset
     *
     * @return Collection|Pagerfanta
     */
    public function load(array $criteria, array $orderBy = [], $limit = 25, $offset = 1);
    
    /**
     * Return the associated DataProvider.
     *
     * @return DataProviderInterface
     */
    public function getDataProvider();
}
