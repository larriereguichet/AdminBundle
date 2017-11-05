<?php

namespace LAG\AdminBundle\Admin;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;

interface AdminInterface
{
    /**
     * Do not load entities on handleRequest (for create method for example)
     */
    const LOAD_STRATEGY_NONE = 'strategy_none';

    /**
     * Load one entity on handleRequest (edit method for example)
     */
    const LOAD_STRATEGY_UNIQUE = 'strategy_unique';

    /**
     * Load multiple entities on handleRequest (list method for example)
     */
    const LOAD_STRATEGY_MULTIPLE = 'strategy_multiple';
    
    /**
     * Handle current request :
     *  - load entities
     *  - create form if required
     *
     * @param Request $request
     *
     * @param array   $filters
     *
     * @return void
     */
    public function handleRequest(Request $request, array $filters = []);

    /**
     * Load entities according to given criteria. OrderBy, limit and offset can be used.
     *
     * @param array $criteria
     * @param array $orderBy
     * @param null $limit
     * @param null $offset
     */
    public function load(array $criteria = [], array $orderBy = [], $limit = null, $offset = null);

    /**
     * Save loaded entities.
     */
    public function save();

    /**
     * Remove loaded entities.
     */
    public function remove();

    /**
     * Create a new entity
     *
     * @return object
     */
    public function create();
    
    /**
     * Return the total number of entities managed by the Admin.
     *
     * @return int
     */
    public function count();

    /**
     * Return admin name.
     *
     * @return string
     */
    public function getName();

    /**
     * @return Collection|mixed
     */
    public function getEntities();

    /**
     * Return an unique entity for current admin. If entity does not exist, it throws an exception.
     *
     * @return mixed
     */
    public function getUniqueEntity();

    /**
     * @return AdminConfiguration
     */
    public function getConfiguration();
    
    /**
     * @return ViewInterface
     */
    public function getView();
    
    /**
     * @return bool
     */
    public function hasView();
}
