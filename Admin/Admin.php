<?php

namespace LAG\AdminBundle\Admin;

use ArrayIterator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use LAG\AdminBundle\Admin\Behaviors\AdminTrait;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Exception\AdminException;
use LAG\AdminBundle\Message\MessageHandlerInterface;
use LAG\DoctrineRepositoryBundle\Repository\RepositoryInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Traversable;

class Admin implements AdminInterface
{
    use AdminTrait;

    const LOAD_METHOD_QUERY_BUILDER = 'query_builder';
    const LOAD_METHOD_UNIQUE_ENTITY = 'unique_entity';
    const LOAD_METHOD_MULTIPLE_ENTITIES = 'multiple';
    const LOAD_METHOD_MANUAL = 'manual';

    /**
     * Entities collection.
     *
     * @var ArrayCollection|ArrayIterator
     */
    protected $entities;

    /**
     * @var MessageHandlerInterface
     */
    protected $messageHandler;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var DataProviderInterface
     */
    protected $dataProvider;

    /**
     * Admin configuration object
     *
     * @var AdminConfiguration
     */
    protected $configuration;

    /**
     * Admin configured actions
     *
     * @var ActionInterface[]
     */
    protected $actions = [];

    /**
     * Admin current action. It will be set after calling the handleRequest()
     *
     * @var ActionInterface
     */
    protected $currentAction;

    /**
     * Admin name
     *
     * @var string
     */
    protected $name;

    /**
     * Admin constructor.
     *
     * @param string $name
     * @param DataProviderInterface $dataProvider
     * @param AdminConfiguration $configuration
     * @param MessageHandlerInterface $messageHandler
     */
    public function __construct(
        $name,
        DataProviderInterface $dataProvider,
        AdminConfiguration $configuration,
        MessageHandlerInterface $messageHandler
    ) {
        $this->name = $name;
        $this->dataProvider = $dataProvider;
        $this->configuration = $configuration;
        $this->messageHandler = $messageHandler;
        $this->entities = new ArrayCollection();
    }

    /**
     * Load entities and set current action according to request
     *
     * @param Request $request
     * @param null $user
     * @return mixed|void
     * @throws AdminException
     */
    public function handleRequest(Request $request, $user = null)
    {
        // set current action
        $this->currentAction = $this->getAction($request->get('_route_params')['_action']);
        // check if user is logged have required permissions to get current action
        $this->checkPermissions($user);
        // load entities according to action and request
        $this->loadEntities($request);
    }

    /**
     * Check if user is allowed to be here
     *
     * @param UserInterface|string $user
     */
    public function checkPermissions($user)
    {
        if (!$user) {
            return;
        }
        $roles = $user->getRoles();
        $actionName = $this
            ->getCurrentAction()
            ->getName();

        if (!$this->isActionGranted($actionName, $roles)) {
            $message = sprintf('User with roles %s not allowed for action "%s"',
                implode(', ', $roles),
                $actionName
            );
            throw new NotFoundHttpException($message);
        }
    }

    /**
     * Save entity via admin manager. Error are catch, logged and a flash message is added to session
     *
     * @return bool true if the entity was saved without errors
     */
    public function save()
    {
        try {
            foreach ($this->entities as $entity) {
                $this
                    ->dataProvider
                    ->save($entity);
            }
            // inform user everything went fine
            $this
                ->messageHandler
                ->handleSuccess('lag.admin.' . $this->name . '.saved');
            $success = true;
        } catch (Exception $e) {
            $this
                ->messageHandler
                ->handleError(
                    'lag.admin.saved_errors',
                    "An error has occurred while saving an entity : {$e->getMessage()}, stackTrace: {$e->getTraceAsString()}"
                );
            $success = false;
        }
        return $success;
    }

    /**
     * Remove an entity with data provider
     *
     * @return bool true if the entity was saved without errors
     */
    public function remove()
    {
        try {
            foreach ($this->entities as $entity) {
                $this
                    ->dataProvider
                    ->remove($entity);
            }
            // inform user everything went fine
            $this
                ->messageHandler
                ->handleSuccess('lag.admin.' . $this->name . '.deleted');
            $success = true;
        } catch (Exception $e) {
            $this
                ->messageHandler
                ->handleError(
                    'lag.admin.deleted_errors',
                    "An error has occurred while deleting an entity : {$e->getMessage()}, stackTrace: {$e->getTraceAsString()} "
                );
            $success = false;
        }
        return $success;
    }

    /**
     * Generate a route for admin and action name (like lag.admin.my_admin)
     *
     * @param $actionName
     *
     * @return string
     *
     * @throws Exception
     */
    public function generateRouteName($actionName)
    {
        if (!array_key_exists($actionName, $this->getConfiguration()->getActions())) {
            $message = 'Invalid action name %s for admin %s (available action are: %s)';
            throw new Exception(sprintf($message, $actionName, $this->getName(), implode(', ', $this->getActionNames())));
        }
        // get routing name pattern
        $routingPattern = $this->getConfiguration()->getRoutingNamePattern();
        // replace admin and action name in pattern
        $routeName = str_replace('{admin}', Container::underscore($this->getName()), $routingPattern);
        $routeName = str_replace('{action}', $actionName, $routeName);

        return $routeName;
    }

    /**
     * Load entities manually according to criteria.
     *
     * @param array $criteria
     * @param array $orderBy
     * @param null $limit
     * @param null $offset
     */
    public function load(array $criteria, $orderBy = [], $limit = null, $offset = null)
    {
        $this->entities = $this
            ->dataProvider
            ->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Load entities according to action load method.
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
        } else if ($loadMethod == self::LOAD_METHOD_MANUAL) {
            // wait for manual load
            $this->entities = [];
        } else {
            throw new AdminException("Unhandled entities load method {$loadMethod} for admin {$this->getName()}");
        }
        return $this->entities;
    }

    /**
     * Load entities by creating a query builder for PagerFanta according to request parameter.
     *
     * @param Request $request
     * @return array|Traversable
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
        if (!method_exists($this->repository, 'createQueryBuilder')) {
            throw new Exception('Method createQueryBuilder not found in class ' . get_class($this->repository));
        }
        // creating query builder from repository
        $this->queryBuilder = $this
            ->repository
            ->createQueryBuilder('entity', 'entity.id');

        if ($sort) {
            // fix bug with join column sort in paging
            if (!$this->configuration->getMetadata()->hasAssociation($sort)) {
                $this
                    ->queryBuilder
                    ->addOrderBy('entity.' . $sort, $order);
            }
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

            if ($regex == false) {
                $criteria[$name] = $value;
            } else if ($value && preg_match("/{$regex}/", $value)) {
                $criteria[$name] = $value;
            }
        }
        if ($unique) {
            // find entity according to criteria
            $entity = $this
                ->repository
                ->findOneBy($criteria);
            // returning a collection to be more generic
            $entities = new ArrayCollection();
            $entities->set($entity->getId(), $entity);
        } else {
            $entities = $this
                ->repository
                ->findBy($criteria);
        }
        return $entities;
    }

    /**
     * Return loaded entities
     *
     * @return mixed
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * Return entity for current admin. If entity does not exist, it throws an exception.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getUniqueEntity()
    {
        if ($this->entities->count() != 1) {
            throw new Exception("Entity not found in admin \"{$this->getName()}\".");
        }
        return $this->entities->first();
    }

    /**
     * @return EntityRepository|RepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Return admin name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }



    /**
     * Return true if current action is granted for user.
     *
     * @param string $actionName Le plus grand de tous les héros
     * @param array $roles
     *
     * @return bool
     */
    public function isActionGranted($actionName, array $roles)
    {
        $isGranted = array_key_exists($actionName, $this->actions);

        // if action exists
        if ($isGranted) {
            $isGranted = false;
            /** @var Action $action */
            $action = $this->actions[$actionName];
            // checking roles permissions
            foreach ($roles as $role) {
                if (in_array($role, $action->getPermissions())) {
                    $isGranted = true;
                }
            }
        }

        return $isGranted;
    }

    /**
     * @return ActionInterface[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @return array
     */
    public function getActionNames()
    {
        return array_keys($this->actions);
    }

    /**
     * @param $name
     * @return ActionInterface
     * @throws Exception
     */
    public function getAction($name)
    {
        if (!array_key_exists($name, $this->getActions())) {
            throw new Exception("Invalid action name \"{$name}\" for admin '{$this->getName()}'");
        }

        return $this->actions[$name];
    }

    /**
     * Return if an action with specified name exists form this admin.
     *
     * @param $name
     * @return bool
     */
    public function hasAction($name)
    {
        return array_key_exists($name, $this->actions);
    }

    /**
     * @param ActionInterface $action
     * @return void
     */
    public function addAction(ActionInterface $action)
    {
        $this->actions[$action->getName()] = $action;
    }

    /**
     * @return ActionInterface
     */
    public function getCurrentAction()
    {
        return $this->currentAction;
    }

    /**
     * Return admin configuration object
     *
     * @return AdminConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
