<?php

namespace BlueBear\AdminBundle\Admin;

use BlueBear\AdminBundle\Manager\GenericManager;
use BlueBear\BaseBundle\Behavior\StringUtilsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Exception;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Admin
{
    use StringUtilsTrait, ActionTrait;

    /**
     * Admin name
     *
     * @var string
     */
    protected $name;

    /**
     * Full namespace for Admin entity
     *
     * @var string
     */
    protected $entityNamespace;

    /**
     * Entities collection
     *
     * @var ArrayCollection
     */
    protected $entities;

    /**
     * Entity
     *
     * @var Object
     */
    protected $entity;

    /**
     * Entity manager (doctrine entity manager by default)
     *
     * @var GenericManager
     */
    protected $manager;

    /**
     * @var AdminConfig
     */
    protected $configuration;

    /**
     * Actions called when using custom manager
     *
     * @var array
     */
    protected $customManagerActions;

    /**
     * Entity repository
     *
     * @var EntityRepository
     */
    protected $repository;

    /**
     * Controller
     *
     * @var Controller
     */
    protected $controller;

    /**
     * Form type
     *
     * @var string
     */
    protected $formType;

    protected $pager;

    public function __construct($name, $repository, $manager, AdminConfig $adminConfig)
    {
        $this->name = $name;
        $this->repository = $repository;
        $this->manager = $manager;
        $this->configuration = $adminConfig;
        $this->controller = $adminConfig->controllerName;
        $this->entityNamespace = $adminConfig->entityName;
        $this->formType = $adminConfig->formType;
        $this->entities = new ArrayCollection();
        $this->customManagerActions = [];
        // pagination
        $adapter = new DoctrineORMAdapter($this->getManager()->getFindAllQueryBuilder());
        // create paginator
        $this->pager = new Pagerfanta($adapter);
    }

    /**
     * Generate a route for admin and action name
     *
     * @param $actionName
     * @return string
     * @throws Exception
     */
    public function generateRouteName($actionName)
    {
        if (!in_array($actionName, array_keys($this->configuration->actions))) {
            throw new Exception("Invalid action name \"{$actionName}\" for admin \"{$this->name}\" (available action are: \""
                . implode('", "', array_keys($this->configuration->actions)) . "\")");
        }
        return 'bluebear_admin_' . $this->underscore($this->getName()) . '_' . $actionName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return entity path for routing (for example, MyNamespace\EntityName => entityName)
     *
     * @return string
     */
    public function getEntityPath()
    {
        $array = explode('\\', $this->getEntityNamespace());
        $path = array_pop($array);
        $path = strtolower(substr($path, 0, 1)) . substr($path, 1);

        return $path;
    }

    /**
     * @return mixed
     */
    public function getEntityNamespace()
    {
        return $this->entityNamespace;
    }

    /**
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return mixed
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @param mixed $entities
     */
    public function setEntities($entities)
    {
        $this->entities = $entities;
    }

    /**
     * @return mixed
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getEntity()
    {
        if (!$this->entity) {
            throw new Exception("Entity not found in admin \"{$this->getName()}\". Try call method findEntity or createEntity first.");
        }
        return $this->entity;
    }

    public function getEntityLabel()
    {
        $label = '';

        if (method_exists($this->entity, 'getLabel')) {
            $label = $this->entity->getLabel();
        } else if (method_exists($this->entity, 'getTitle')) {
            $label = $this->entity->getTitle();
        } else if (method_exists($this->entity, 'getName')) {
            $label = $this->entity->getName();
        } else if (method_exists($this->entity, '__toString')) {
            $label = $this->entity->__toString();
        }
        return $label;
    }

    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    public function findEntity($field, $value)
    {
        $this->entity = $this->getManager()->findOneBy([
            $field => $value
        ]);
        $this->checkEntity();
        return $this->entity;
    }

    public function findEntities($page = 1)
    {
        $this->pager->setMaxPerPage($this->configuration->maxPerPage);
        $this->pager->setCurrentPage($page);
        $this->entities = $this->pager->getCurrentPageResults();

        return $this->entities;
    }

    public function saveEntity()
    {
        $this->checkEntity();
        $this->getManager()->save($this->entity);
    }

    public function createEntity()
    {
        $this->entity = $this->getManager()->create($this->getEntityNamespace());
        $this->checkEntity();

        return $this->entity;
    }

    public function deleteEntity()
    {
        $this->checkEntity();
        $this->getManager()->delete($this->entity);
    }

    /**
     * @return GenericManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    protected function checkEntity()
    {
        if (!$this->entity) {
            throw new Exception("Entity not found in admin \"{$this->getName()}\". Try call method findEntity or createEntity first.");
        }
    }

    /**
     * @return AdminConfig
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return Pagerfanta
     */
    public function getPager()
    {
        return $this->pager;
    }
}