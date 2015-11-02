<?php

namespace LAG\AdminBundle\Admin;

use ArrayIterator;
use LAG\AdminBundle\Admin\Behaviors\ActionTrait;
use LAG\AdminBundle\Admin\Behaviors\AdminTrait;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use BlueBear\BaseBundle\Behavior\StringUtilsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Exception;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class Admin implements AdminInterface
{
    use StringUtilsTrait, ActionTrait, AdminTrait;

    const LOAD_METHOD_QUERY_BUILDER = 'query_builder';
    const LOAD_METHOD_UNIQUE_ENTITY = 'unique_entity';
    const LOAD_METHOD_MULTIPLE_ENTITIES = 'multiple';

    /**
     * Entities collection.
     *
     * @var ArrayCollection|ArrayIterator
     */
    protected $entities;

    /**
     * Actions called when using custom manager.
     *
     * @var array
     */
    protected $customManagerActions;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param $name
     * @param EntityRepository $repository
     * @param ManagerInterface $manager
     * @param AdminConfiguration $adminConfig
     * @param Session $session
     * @param LoggerInterface $logger
     */
    public function __construct(
        $name,
        EntityRepository $repository,
        ManagerInterface $manager,
        AdminConfiguration $adminConfig,
        Session $session,
        LoggerInterface $logger
    )
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
        $this->session = $session;
        $this->logger = $logger;
    }

    /**
     * Load entities and set current action according to request
     *
     * @param Request $request
     * @return void
     * @throws AdminException
     * @throws Exception
     */
    public function handleRequest(Request $request)
    {
        // set current action
        $action = $this->getAction($request->get('_route_params')['_action']);
        $this->currentAction = $action;
        // load entities according to action and request
        $this->loadEntities($request);
    }

    /**
     * Save entity via admin manager. Error are catch, logged and an flash message is added.
     *
     * @return bool true if the entity was saved without errors
     */
    public function save()
    {
        $success = false;

        try {
            $this
                ->manager
                ->save($this->getEntity());
            // inform user everything went fine
            $this
                ->session
                ->getFlashBag()
                ->add('info', 'lag.admin.' . $this->name . '.saved');
            $success = true;
        } catch (Exception $e) {
            $this
                ->logger
                ->error("An error has occured during saving an entity : {$e->getMessage()}, stackTrace: {$e->getTraceAsString()} ");
            $this
                ->session
                ->getFlashBag()
                ->add('error', 'lag.admin.saved_errors');
        }
        return $success;
    }

    /**
     * Delete entity via admin manager. Error are catch, logged and an flash message is added.
     *
     * @return bool true if the entity was saved without errors
     */
    public function delete()
    {
        $success = false;

        try {
            $this
                ->manager
                ->delete($this->getEntity());
            // inform user everything went fine
            $this
                ->session
                ->getFlashBag()
                ->add('info', 'lag.admin.' . $this->name . '.deleted');
            $success = true;
        } catch (Exception $e) {
            $this
                ->logger
                ->error("An error has occured during deleting an entity : {$e->getMessage()}, stackTrace: {$e->getTraceAsString()} ");
            $this
                ->session
                ->getFlashBag()
                ->add('error', 'lag.admin.deleted_errors');
        }
        return $success;
    }

    /**
     * Load entities according to load method of current action
     *
     * @param Request $request
     * @return ArrayIterator|ArrayCollection
     * @throws AdminException
     * @throws Exception
     */
    protected function loadEntities(Request $request)
    {
        $loadMethod = $this
            ->currentAction
            ->getConfiguration()
            ->getLoadMethod();

        if ($loadMethod == self::LOAD_METHOD_QUERY_BUILDER) {
            // loading entities with a query builder
            $this->entities = $this->loadWithQueryBuilder($request);
        } else if ($loadMethod == self::LOAD_METHOD_UNIQUE_ENTITY) {
            // load only one entity from request criteria
            $this->entities = $this->loadWithParameters($request, true);
        } else if ($loadMethod == self::LOAD_METHOD_MULTIPLE_ENTITIES) {
            // load all entities matching request criteria
            $this->entities = $this->loadWithParameters($request, false);
        } else {
            throw new AdminException("Unhandled entities load in method {$loadMethod} in admin {$this->getName()}");
        }

        return $this->entities;
    }

    /**
     * Load entities by creating a query builder for PagerFanta according to request parameter
     *
     * @param Request $request
     * @return array|\Traversable
     * @throws Exception
     */
    protected function loadWithQueryBuilder(Request $request)
    {
        // getting pager parameter
        $page = $request->get('page', 1);
        $sort = $request->get('sort');
        $order = $request->get('order');
        // check if sort field is allowed for current action
        if ($sort) {
            if (!$this->getCurrentAction()->hasField($sort)) {
                throw new Exception("Invalid field \"{$sort}\" for current action \"{$this->getCurrentAction()->getName()}\"");
            }
            // if no sort was used by user, we sort with default configured sort if there is one
            if (!$order) {
                // getting configured order
                $order = $this
                    ->getCurrentAction()
                    ->getConfiguration()
                    ->getOrder();
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

        if ($sort) {
            $this
                ->queryBuilder
                ->addOrderBy('entity.' . $sort, $order);
        }
        // create adapter for query builder
        $adapter = new DoctrineORMAdapter($this->queryBuilder);
        // create pager
        $this->pager = new Pagerfanta($adapter);
        $this->pager->setMaxPerPage($this->configuration->getMaxPerPage());
        $this->pager->setCurrentPage($page);

        return $this
            ->pager
            ->getCurrentPageResults();
    }

    /**
     * Loading an entity according to request parameter
     *
     * @param Request $request
     * @param bool $unique
     * @return array
     */
    protected function loadWithParameters(Request $request, $unique = false)
    {
        $parameters = $this
            ->currentAction
            ->getConfiguration()
            ->getParameters();
        $criteria = [];

        foreach ($parameters as $name => $regex) {
            $value = $request->get($name);

            if ($value && preg_match("/{$regex}/", $value)) {
                $criteria[$name] = $value;
            }
        }
        if ($unique) {
            // find entity according to criteria
            $entity = $this
                ->manager
                ->getRepository()
                ->findOneBy($criteria);
            // returning a collection to be more generic
            $entities = new ArrayCollection();
            $entities->set($entity->getId(), $entity);
        } else {
            $entities = $this
                ->manager
                ->getRepository()
                ->findBy($criteria);
        }

        return $entities;
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
     * Return entity for current admin. If entity does not exist, it throws an exception.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getEntity()
    {
        if (!$this->entities->count()) {
            throw new Exception("Entity not found in admin \"{$this->getName()}\". Try call method findEntity or createEntity first.");
        }
        return $this->entities->first();
    }


}
