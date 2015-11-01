<?php

namespace LAG\AdminBundle\Admin;

use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Manager\GenericManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Exception;
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

    public function getEntityLabel();

    public function setEntity($entity);

    /**
     * Find a entity by one of its field.
     *
     * @param $field
     * @param $value
     *
     * @return null|object
     *
     * @throws Exception
     */
    public function findEntity($field, $value);

    /**
     * Find entities paginated and sorted.
     *
     * @param int    $page
     * @param null   $sort
     * @param string $order
     *
     * @return array|ArrayCollection|\Traversable
     *
     * @throws Exception
     */
    public function findEntities($page = 1, $sort = null, $order = 'ASC');

    public function saveEntity();

    public function createEntity();

    public function deleteEntity();

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
     * Return current admin Action.
     *
     * @return Action
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
