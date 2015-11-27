<?php

namespace LAG\AdminBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Exception;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Manager\GenericManager;
use Doctrine\ORM\EntityRepository;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

interface AdminInterface
{
    /**
     * Handle current request :
     *  - load entities
     *  - create form if required
     *
     * @param Request $request
     * @return mixed
     */
    public function handleRequest(Request $request);

    /**
     * Generate a route for admin and action name.
     *
     * @param $actionName
     *
     * @return string
     *
     * @throws Exception
     */
    public function generateRouteName($actionName);

    /**
     * Load entities according to given criteria. OrderBy, limit and offset can be used
     *
     * @param array $criteria
     * @param array $orderBy
     * @param null $limit
     * @param null $offset
     */
    public function load(array $criteria, $orderBy = [], $limit = null, $offset = null);

    /**
     * Save loaded entities using manager
     */
    public function save();

    /**
     * Delete loaded entities using manager
     */
    public function delete();

    /**
     * Return admin name.
     *
     * @return string
     */
    public function getName();

    /**
     * @return mixed
     */
    public function getEntityNamespace();

    /**
     * @return EntityRepository
     */
    public function getRepository();

    /**
     * @return mixed
     */
    public function getEntities();

    /**
     * @return mixed
     */
    public function getFormType();

    /**
     * @return mixed
     */
    public function getController();

    /**
     * Return an unique entity for current admin. If entity does not exist, it throws an exception.
     *
     * @return mixed
     */
    public function getUniqueEntity();

    /**
     * @return GenericManager
     */
    public function getManager();

    /**
     * @return AdminConfiguration
     */
    public function getConfiguration();

    /**
     * @return Pagerfanta
     */
    public function getPager();

    public function getActions();

    /**
     * @param $actionName
     * @return ActionInterface
     */
    public function getAction($actionName);

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
}
