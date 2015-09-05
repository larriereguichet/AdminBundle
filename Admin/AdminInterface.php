<?php

namespace BlueBear\AdminBundle\Admin;

use BlueBear\AdminBundle\Admin\Configuration\AdminConfiguration;
use BlueBear\AdminBundle\Manager\GenericManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Exception;
use Pagerfanta\Pagerfanta;

interface AdminInterface
{
    /**
     * Return admin name
     *
     * @return string
     */
    public function getName();

    /**
     * Return entity path for routing (for example, MyNamespace\EntityName => entityName)
     *
     * @return string
     */
    public function getEntityPath();

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
     * Return entity for current admin. If entity does not exist, it throws an exception
     *
     * @return mixed
     */
    public function getEntity();

    public function getEntityLabel();

    public function setEntity($entity);

    /**
     * Find a entity by one of its field
     *
     * @param $field
     * @param $value
     * @return null|object
     * @throws Exception
     */
    public function findEntity($field, $value);

    /**
     * Find entities paginated and sorted
     *
     * @param int $page
     * @param null $sort
     * @param string $order
     * @return array|ArrayCollection|\Traversable
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
}
