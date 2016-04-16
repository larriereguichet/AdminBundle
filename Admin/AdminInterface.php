<?php

namespace LAG\AdminBundle\Admin;

use Exception;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

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
     * @param UserInterface $user
     * @return mixed
     */
    public function handleRequest(Request $request, $user = null);

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
    public function load(array $criteria, $orderBy = [], $limit = null, $offset = null);

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
     * Return admin name.
     *
     * @return string
     */
    public function getName();

    /**
     * @return mixed
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
     * Return true if current action is granted for user.
     *
     * @param string $actionName
     * @param array  $roles
     *
     * @return bool
     */
    public function isActionGranted($actionName, array $roles);

    /**
     * Try to find a property to get a label from an entity. If found, it returns the property value through the
     * property accessor.
     *
     * @return string
     */
    public function getEntityLabel();
}
