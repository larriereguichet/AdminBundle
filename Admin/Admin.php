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

class Admin implements AdminInterface
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
        if (!array_key_exists($actionName, $this->getConfiguration()->actions)) {
            throw new Exception("Invalid action name \"{$actionName}\" for admin \"{$this->getName()}\" (available action are: \""
                . implode('", "', array_keys($this->getConfiguration()->actions)) . "\")");
        }
        // get routing name pattern
        $routingPattern = $this->configuration->routingNamePattern;
        // replace admin and action name in pattern
        $routeName = str_replace('{admin}', $this->underscore($this->getName()), $routingPattern);
        $routeName = str_replace('{action}', $actionName, $routeName);

        return $routeName;
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
     * Return entity for current admin. If entity does not exist, it throws an exception
     *
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

    /**
     * Find a entity by one of its field
     *
     * @param $field
     * @param $value
     * @return null|object
     * @throws Exception
     */
    public function findEntity($field, $value)
    {
        $this->entity = $this->getManager()->findOneBy([
            $field => $value
        ]);
        $this->checkEntity();
        return $this->entity;
    }

    /**
     * Find entities paginated and sorted
     *
     * @param int $page
     * @param null $sort
     * @param string $order
     * @return array|ArrayCollection|\Traversable
     * @throws Exception
     */
    public function findEntities($page = 1, $sort = null, $order = 'ASC')
    {
        if ($sort) {
            // check if sort field is allowed for current action
            if (!$this->getCurrentAction()->hasField($sort)) {
                throw new Exception("Invalid field \"{$sort}\" for current action \"{$this->getCurrentAction()->getName()}\"");
            }
            if (!in_array($order, ['ASC', 'DESC'])) {
                throw new Exception("Invalid order \"{$order}\"");
            }
        }
        // create adapter from query builder
        $adapter = new DoctrineORMAdapter($this->getManager()->getFindAllQueryBuilder($sort, $order));
        // create pager
        $this->pager = new Pagerfanta($adapter);
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

    protected function checkEntity()
    {
        if (!$this->entity) {
            throw new Exception("Entity not found in admin \"{$this->getName()}\". Try call method findEntity or createEntity first.");
        }
    }
}
