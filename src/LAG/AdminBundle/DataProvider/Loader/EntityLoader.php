<?php

namespace LAG\AdminBundle\DataProvider\Loader;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Repository\RepositoryInterface;
use LogicException;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * The EntityLoader is responsible for loading one or multiple entities from the repository, and if a pagination
 * system is required.
 */
class EntityLoader
{
    /**
     * The name of the pagination system. Only pagerfanta is yet supported.
     *
     * @var string|null
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
     * The repository where the entities are stored.
     *
     * @var RepositoryInterface
     */
    private $repository;
    
    /**
     * EntityLoader constructor.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * @param ActionConfiguration $configuration
     */
    public function configure(ActionConfiguration $configuration)
    {
        $this->loadStrategy = $configuration->getParameter('load_strategy');
        $this->pagerName = $configuration->getParameter('pager');
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
        if ($this->isPaginationRequired()) {
            // load entities from the DataProvider using a pagination system
            $entities = $this->loadPaginate($criteria, $orderBy, $limit, $offset);
        }
        else {
            // if no pagination is required (edit action for example)
            $entities = $this->loadWithoutPagination($criteria, $orderBy);
        }
    
        // load the entities into the Admin
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
                'Only pagerfanta value is allowed for pager parameter, given "'.$this->pagerName.'"'
            );
        }
    
        // only load strategy multiple is allowed for pagination (ie, can not paginate if only one entity is loaded)
        if (AdminInterface::LOAD_STRATEGY_MULTIPLE !== $this->loadStrategy) {
            throw new LogicException(
                'Only "strategy_multiple" value is allowed for pager parameter, given '.$this->loadStrategy
            );
        }
    
        if (!$this->repository instanceof EntityRepository) {
            throw new LogicException('You can only paginate '.EntityRepository::class);
        }
        $queryBuilder = $this
            ->repository
            ->createQueryBuilder('entity')
        ;
    
        // add criteria
        foreach ($criteria as $criterion => $value) {
            $queryBuilder
                ->andWhere('entity.'.$criterion.' = :'.$criterion)
                ->setParameter($criterion, $value)
            ;
        }
        
        // add order by
        foreach ($orderBy as $sort => $order) {
            $queryBuilder
                ->addOrderBy($sort, $order)
            ;
        }
        // create an adapter for the pagerfanta
        $adapter = new DoctrineORMAdapter($queryBuilder->getQuery());
        
        // create pager
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($offset);
        
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
            ->repository
            ->findBy($criteria, $orderBy, null, null)
        ;
    }
    
    /**
     * Return true if a pagination system is required for the current Action.
     *
     * @return bool
     */
    private function isPaginationRequired()
    {
        if (AdminInterface::LOAD_STRATEGY_MULTIPLE !== $this->loadStrategy) {
            return false;
        }
    
        if ('pagerfanta' !== $this->pagerName) {
            return false;
        }
        
        return true;
    }
}
