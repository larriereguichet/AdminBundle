<?php

namespace LAG\AdminBundle\DataProvider\Loader;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Pager\PagerfantaAdminAdapter;
use LogicException;
use Pagerfanta\Pagerfanta;

/**
 * The EntityLoader is responsible for loading one or multiple entities from the data provider, and if a pagination
 * system is required.
 */
class EntityLoader implements EntityLoaderInterface
{
    /**
     * True if a pagination system is required for the current Action.
     *
     * @var bool
     */
    private $isPaginationRequired = false;
    
    /**
     * The name of the pagination system. Only pagerfanta is yet supported.
     *
     * @var string
     */
    private $pagerName;
    
    /**
     * The loading strategy
     *
     * @see AdminInterface
     *
     * @var string
     */
    private $loadStrategy;
    
    /**
     * The provider used to retrieve data.
     *
     * @var DataProviderInterface
     */
    private $dataProvider;
    
    private $limit = 0;
    
    /**
     * EntityLoader constructor.
     *
     * @param DataProviderInterface $dataProvider
     */
    public function __construct(DataProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }
    
    /**
     * @param ActionConfiguration $configuration
     */
    public function configure(ActionConfiguration $configuration)
    {
        $this->loadStrategy = $configuration->getParameter('load_strategy');
    
        if (AdminInterface::LOAD_STRATEGY_MULTIPLE === $this->loadStrategy &&
            $configuration->getParameter('pager')) {
            $this->isPaginationRequired = true;
        }
        $this->pagerName = $configuration->getParameter('pager');
        $this->limit = $configuration->getParameter('max_per_page');
    }
    
    /***
     * @param array $criteria
     * @param array $orderBy
     * @param int   $limit
     * @param int   $offset
     *
     * @return array|Collection|Pagerfanta
     */
    public function load(array $criteria, array $orderBy = [], $limit = 25, $offset = 1)
    {
        if (false !== $this->isPaginationRequired) {
            // Load entities from the DataProvider using a pagination system
            $entities = $this->loadPaginate($criteria, $orderBy, $limit, $offset);
        }
        else {
            // If no pagination is required (edit action for example)
            $entities = $this->loadWithoutPagination($criteria, $orderBy);
        }
        
        return $entities;
    }
    
    /**
     * Load entities using Pagerfanta.
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int   $limit
     * @param int   $offset
     *
     * @return Pagerfanta
     */
    private function loadPaginate(array $criteria, array $orderBy, $limit, $offset)
    {
        // only pagerfanta adapter is yet supported
        if ('pagerfanta' !== $this->pagerName) {
            throw new LogicException(
                'Only pagerfanta value is allowed for pager parameter, given '.$this->pagerName
            );
        }
    
        // only load strategy multiple is allowed for pagination (ie, can not paginate if only one entity is loaded)
        if (AdminInterface::LOAD_STRATEGY_MULTIPLE !== $this->loadStrategy) {
            throw new LogicException(
                'Only "strategy_multiple" value is allowed for pager parameter, given '.$this->loadStrategy
            );
        }
    
        if (null === $limit) {
            $limit = $this->limit;
        }
    
        if (null === $offset) {
            $offset = 1;
        }
    
        // adapter to pagerfanta
        $adapter = new PagerfantaAdminAdapter($this->dataProvider, $criteria, $orderBy);
        // create pager
        $pager = new Pagerfanta($adapter);
        $pager->setCurrentPage($offset);
        $pager->setMaxPerPage($limit);
    
        //dump($pager->getCurrentPageResults()[0]->getTitle());
        
        return $pager;
    }
    
    /**
     * Load entities using to configured data provider, without using a pagination system.
     *
     * @param array $criteria
     * @param array $orderBy
     *
     * @return array|Collection
     */
    private function loadWithoutPagination(array $criteria, $orderBy)
    {
        return $this
            ->dataProvider
            ->findBy($criteria, $orderBy, null, null)
        ;
    }
    
    /**
     * Return the associated DataProvider.
     *
     * @return DataProviderInterface
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }
}
