<?php

namespace LAG\AdminBundle\Admin;

use ArrayIterator;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Admin\Behaviors\ActionTrait;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use BlueBear\BaseBundle\Behavior\StringUtilsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Exception;
use LAG\AdminBundle\Form\Type\AdminListType;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class Admin implements AdminInterface
{
    use StringUtilsTrait, ActionTrait;

    const LOAD_METHOD_QUERY_BUILDER = 'query_builder';
    const LOAD_METHOD_UNIQUE_ENTITY = 'unique_entity';
    const LOAD_METHOD_MULTIPLE_ENTITIES = 'multiple';

    /**
     * Admin name.
     *
     * @var string
     */
    protected $name;

    /**
     * Full namespace for Admin entity.
     *
     * @var string
     */
    protected $entityNamespace;

    /**
     * @var ActionInterface
     */
    protected $currentAction;

    /**
     * Entities collection.
     *
     * @var ArrayCollection|ArrayIterator
     */
    protected $entities;

    /**
     * Entity.
     *
     * @var Object
     */
    protected $entity;

    /**
     * Entity manager (doctrine entity manager by default).
     *
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @var AdminConfiguration
     */
    protected $configuration;

    /**
     * Actions called when using custom manager.
     *
     * @var array
     */
    protected $customManagerActions;

    /**
     * Entity repository.
     *
     * @var EntityRepository
     */
    protected $repository;

    /**
     * Controller.
     *
     * @var Controller
     */
    protected $controller;

    /**
     * Form type.
     *
     * @var string
     */
    protected $formType;

    /**
     * @var Pagerfanta
     */
    protected $pager;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @param $name
     * @param $repository
     * @param $manager
     * @param AdminConfiguration $adminConfig
     */
    public function __construct($name, $repository, $manager, AdminConfiguration $adminConfig)
    {
        $this->name = $name;
        $this->repository = $repository;
        $this->manager = $manager;
        $this->configuration = $adminConfig;
        $this->controller = $adminConfig->getControllerName();
        $this->entityNamespace = $adminConfig->getEntityName();
        $this->formType = $adminConfig->getFormType();
        $this->entities = new ArrayCollection();
        $this->customManagerActions = [];
    }

    public function handleRequest(Request $request)
    {
        // set current action
        $action = $this->getAction($request->get('_route_params')['_action']);
        $this->currentAction = $action;
        // load entities according to action and request
        $this->loadEntities($request->get('page', 1), $request->get('sort'), $request->get('order'));
    }

    protected function loadEntities($page = 1, $sort = null, $order = 'ASC')
    {
        // loading entities with a query builder
        if ($this->currentAction->getConfiguration()->getLoadMethod() == self::LOAD_METHOD_QUERY_BUILDER) {
            $this->loadWithQueryBuilder($page, $sort, $order);
        }
        else if ($this->currentAction->getConfiguration()->getLoadMethod() == self::LOAD_METHOD_UNIQUE_ENTITY) {
            $this->entities = '';
        }

        return $this->entities;
    }

    protected function loadWithQueryBuilder($page, $sort, $order)
    {
        // check if sort field is allowed for current action
        if ($sort) {
            if (!$this->getCurrentAction()->hasField($sort)) {
                throw new Exception("Invalid field \"{$sort}\" for current action \"{$this->getCurrentAction()->getName()}\"");
            }
            if (!in_array($order, ['ASC', 'DESC'])) {
                throw new Exception("Invalid order \"{$order}\". Only ASC and DESC are allowed");
            }
        }
        // creating query builder from repository
        $this->queryBuilder = $this
            ->manager
            ->getRepository()
            ->createQueryBuilder('entity', 'entity.id');
        // getting configured order
        $order = $this
            ->getCurrentAction()
            ->getConfiguration()
            ->getOrder();

        // if no sort was used by user, we sort with default configured sort if there is one
        if ($order) {
            foreach ($order as $orderConfiguration) {
                $this->queryBuilder
                    ->addOrderBy('entity.' . $orderConfiguration['field'], $orderConfiguration['order']);
            }
        }
        // create adapter for query builder
        $adapter = new DoctrineORMAdapter($this->queryBuilder);
        // create pager
        $this->pager = new Pagerfanta($adapter);
        $this->pager->setMaxPerPage($this->configuration->getMaxPerPage());
        $this->pager->setCurrentPage($page);
        // loading current entities
        $this->entities = $this->pager->getCurrentPageResults();
    }

    /**
     * @return ActionInterface
     */
    public function getCurrentAction()
    {
        return $this->currentAction;
    }

    protected function loadWithParameters(Request $request)
    {

    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * Return entity for current admin. If entity does not exist, it throws an exception.
     *
     * @return mixed
     *
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
        } elseif (method_exists($this->entity, 'getTitle')) {
            $label = $this->entity->getTitle();
        } elseif (method_exists($this->entity, 'getName')) {
            $label = $this->entity->getName();
        } elseif (method_exists($this->entity, '__toString')) {
            $label = $this->entity->__toString();
        }

        return $label;
    }

    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

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
    public function findEntity($field, $value)
    {
        $this->entity = $this->getManager()->getRepository()->findOneBy([
            $field => $value,
        ]);
        $this->checkEntity();

        return $this->entity;
    }

    public function saveEntity()
    {
        $this->checkEntity();
        $this->getManager()->save($this->entity);
    }

    public function createEntity()
    {
        $this->entity = $this->getManager()->create();
        $this->checkEntity();

        return $this->entity;
    }

    public function deleteEntity()
    {
        // TODO handle integrity constraint
        $this->checkEntity();
        $this->getManager()->delete($this->entity);
    }

    /**
     * @return ManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @return AdminConfiguration
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

    public function createListForm(FormFactoryInterface $formFactory)
    {
        $form = $formFactory->create(new AdminListType(), [
            'entities' => $this->entities
        ], [
            'item_class' => $this->entityNamespace,
            'batch_actions' => $this->currentAction->getConfiguration()->getBatch(),
            'query_builder' => $this->queryBuilder
        ]);

        return $form;
    }

    protected function checkEntity()
    {
        if (!$this->entity) {
            throw new Exception("Entity not found in admin \"{$this->getName()}\". Try call method findEntity or createEntity first.");
        }
    }
}
