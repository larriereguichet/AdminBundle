<?php

namespace LAG\AdminBundle\Admin;

use Doctrine\Common\Collections\Collection;
use Exception;
use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormInterface;
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
     * Generate a route for an action.
     *
     * @param $actionName
     *
     * @return string
     *
     * @throws Exception
     */
    public function generateRouteName($actionName);

    /**
     * Load entities according to given criteria. OrderBy, limit and offset can be used.
     *
     * @param array $criteria
     * @param array $orderBy
     * @param null $limit
     * @param null $offset
     */
    public function load(array $criteria, array $orderBy = [], $limit = null, $offset = null);

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
     * @return Pagerfanta
     */
    public function getPager();

    /**
     * @return ActionInterface[]
     */
    public function getActions();
    
    /**
     * Return true if the Action with name $name exists in the Admin. If the method return true, it does not necessarily
     * means that the action is allowed in the current context.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasAction($name);

    /**
     * @param $actionName
     * @return ActionInterface
     */
    public function getAction($actionName);

    /**
     * @param ActionInterface $actionInterface
     * @return mixed
     */
    public function addAction(ActionInterface $actionInterface);

    /**
     * Return current admin Action.
     *
     * @return ActionInterface
     */
    public function getCurrentAction();

    /**
     * Return if the current action has been initialized and set.
     *
     * @return boolean
     */
    public function isCurrentActionDefined();

    /**
     * Try to find a property to get a label from an unique entity. If found, it returns the property value through the
     * property accessor.
     *
     * @return string
     */
    public function getUniqueEntityLabel();
    
    /**
     * Return true if the current filter form has been set.
     *
     * @return bool
     */
    public function hasFilterForm();
    
    /**
     * Return the filter form. If the form is not initialized, could throw an Exception.
     *
     * @return FormInterface
     */
    public function getFilterForm();
}
