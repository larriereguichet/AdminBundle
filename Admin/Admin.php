<?php

namespace LAG\AdminBundle\Admin;

use ArrayIterator;
use LAG\AdminBundle\Admin\Behaviors\ActionTrait;
use LAG\AdminBundle\Admin\Behaviors\AdminTrait;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Exception;
use LAG\AdminBundle\Admin\Message\MessageHandler;
use LAG\AdminBundle\Exception\AdminException;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Traversable;

class Admin implements AdminInterface
{
    use ActionTrait, AdminTrait;

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
     * @var MessageHandler
     */
    protected $messageHandler;

    /**
     * @param $name
     * @param EntityRepository $repository
     * @param ManagerInterface $manager
     * @param AdminConfiguration $adminConfig
     * @param MessageHandler $messageHandler
     */
    public function __construct(
        $name,
        EntityRepository $repository,
        ManagerInterface $manager,
        AdminConfiguration $adminConfig,
        MessageHandler $messageHandler
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
        $this->messageHandler = $messageHandler;
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
                    ->manager
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
                    "An error has occurred while saving an entity : {$e->getMessage()}, stackTrace: {$e->getTraceAsString()} "
                );
            $success = false;
        }
        return $success;
    }

    /**
     * Delete entity via admin manager. Error are catch, logged and an flash message is added
     *
     * @return bool true if the entity was saved without errors
     */
    public function delete()
    {
        try {
            foreach ($this->entities as $entity) {
                $this
                    ->manager
                    ->delete($entity);
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
     * Load entities manually according to criteria
     *
     * @param array $criteria
     * @param array $orderBy
     * @param null $limit
     * @param null $offset
     */
    public function load(array $criteria, $orderBy = [], $limit = null, $offset = null)
    {
        $this->entities = $this
            ->manager
            ->getRepository()
            ->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Load entities according to action load method
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
     * Load entities by creating a query builder for PagerFanta according to request parameter
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
        // creating query builder from repository
        $this->queryBuilder = $this
            ->manager
            ->getRepository()
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
}
