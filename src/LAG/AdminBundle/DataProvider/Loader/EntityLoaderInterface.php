<?php

namespace LAG\AdminBundle\DataProvider\Loader;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use Traversable;

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
     * @return ArrayCollection|Traversable
     */
    public function load(array $criteria, array $orderBy = [], $limit = 25, $offset = 1);
    
    /**
     * Configure the entity loader.
     *
     * @param ActionConfiguration $configuration
     */
    public function configure(ActionConfiguration $configuration);
    
    /**
     * Return the associated DataProvider.
     *
     * @return DataProviderInterface
     */
    public function getDataProvider();
}
