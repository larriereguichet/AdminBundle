<?php

namespace LAG\AdminBundle\Admin;

use Doctrine\ORM\QueryBuilder;
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
     *  - creating form if required
     *
     * @param Request $request
     * @return mixed
     */
    public function handleRequest(Request $request);

    /**
     * @return void
     */
    public function save();

    /**
     * @return void
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
     * @param mixed $entities
     */
    public function setEntities($entities);

    /**
     * @return mixed
     */
    public function getFormType();

    /**
     * @return mixed
     */
    public function getController();

    /**
     * Return entity for current admin. If entity does not exist, it throws an exception.
     *
     * @return mixed
     */
    public function getEntity();

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

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder();
}
