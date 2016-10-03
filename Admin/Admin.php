<?php

namespace LAG\AdminBundle\Admin;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Admin\Behaviors\AdminTrait;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Exception\AdminException;
use LAG\AdminBundle\Filter\PagerfantaFilter;
use LAG\AdminBundle\Filter\RequestFilter;
use LAG\AdminBundle\Filter\RequestFilterInterface;
use LAG\AdminBundle\Message\MessageHandlerInterface;
use LAG\AdminBundle\Pager\PagerFantaAdminAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

class Admin implements AdminInterface
{
    use AdminTrait;

    /**
     * Entities collection.
     *
     * @var ArrayCollection
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
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var RequestFilterInterface
     */
    protected $requestFilter;

    /**
     * Admin constructor.
     *
     * @param string $name
     * @param DataProviderInterface $dataProvider
     * @param AdminConfiguration $configuration
     * @param MessageHandlerInterface $messageHandler
     * @param EventDispatcherInterface $eventDispatcher
     * @param RequestFilterInterface $requestFilter
     */
    public function __construct(
        $name,
        DataProviderInterface $dataProvider,
        AdminConfiguration $configuration,
        MessageHandlerInterface $messageHandler,
        EventDispatcherInterface $eventDispatcher,
        RequestFilterInterface $requestFilter
    ) {
        $this->name = $name;
        $this->dataProvider = $dataProvider;
        $this->configuration = $configuration;
        $this->messageHandler = $messageHandler;
        $this->eventDispatcher = $eventDispatcher;
        $this->entities = new ArrayCollection();
        $this->requestFilter = $requestFilter;
    }

    /**
     * Load entities and set current action according to request.
     *
     * @param Request $request
     * @param null $user
     * @return void
     * @throws AdminException
     */
    public function handleRequest(Request $request, $user = null)
    {
        // set current action
        $this->currentAction = $this->getAction($request->get('_route_params')['_action']);

        // check if user is logged have required permissions to get current action
        $this->checkPermissions($user);

        $actionConfiguration = $this
            ->currentAction
            ->getConfiguration();

        // configure the request filter with the action and admin configured parameters
        $this
            ->requestFilter
            ->configure(
                $actionConfiguration->getParameter('criteria'),
                $actionConfiguration->getParameter('order'),
                $this->configuration->getParameter('max_per_page')
            );

        // filter the request with the configured criteria, order and max_per_page parameter
        $this
            ->requestFilter
            ->filter($request);

        // load entities according to action and request
        $this->load(
            $this->requestFilter->getCriteria(),
            $this->requestFilter->getOrder(),
            $this->requestFilter->getMaxPerPage(),
            $this->requestFilter->getCurrentPage()
        );
    }

    /**
     * Check if user is allowed to be here
     *
     * @param UserInterface|string $user
     * @throws Exception
     */
    public function checkPermissions($user)
    {
        if (!($user instanceof UserInterface)) {
            return;
        }
        if ($this->currentAction === null) {
            throw new Exception('Current action should be set before checking the permissions');
        }
        $roles = $user->getRoles();
        $actionName = $this
            ->getCurrentAction()
            ->getName();

        if (!$this->isActionGranted($actionName, $roles)) {
            $rolesStringArray = [];

            foreach ($roles as $role) {

                if ($role instanceof Role) {
                    $rolesStringArray[] = $role->getRole();
                } else {
                    $rolesStringArray[] = $role;
                }
            }

            $message = sprintf('User with roles %s not allowed for action "%s"',
                implode(', ', $rolesStringArray),
                $actionName
            );
            throw new NotFoundHttpException($message);
        }
    }

    /**
     * Create and return a new entity.
     *
     * @return object
     */
    public function create()
    {
        // create an entity from the data provider
        $entity = $this
            ->dataProvider
            ->create();

        // add it to the collection
        $this
            ->entities
            ->add($entity);

        return $entity;
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
            // inform the user that the entity is saved
            $this
                ->messageHandler
                ->handleSuccess($this->generateMessageTranslationKey('saved'));
            $success = true;
        } catch (Exception $e) {
            $this
                ->messageHandler
                ->handleError(
                    $this->generateMessageTranslationKey('lag.admin.saved_errors'),
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
            // inform the user that the entity is removed
            $this
                ->messageHandler
                ->handleSuccess($this->generateMessageTranslationKey('deleted'));
            $success = true;
        } catch (Exception $e) {
            $this
                ->messageHandler
                ->handleError(
                    $this->generateMessageTranslationKey('lag.admin.deleted_errors'),
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
        if (!array_key_exists($actionName, $this->getConfiguration()->getParameter('actions'))) {
            throw new Exception(
                sprintf('Invalid action name %s for admin %s (available action are: %s)',
                    $actionName,
                    $this->getName(),
                    implode(', ', $this->getActionNames()))
            );
        }
        // get routing name pattern
        $routingPattern = $this->getConfiguration()->getParameter('routing_name_pattern');
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
     * @param int $limit
     * @param int $offset
     * @throws Exception
     */
    public function load(array $criteria, $orderBy = [], $limit = 25, $offset = 1)
    {
        $actionConfiguration = $this
            ->getCurrentAction()
            ->getConfiguration();
        $pager = $actionConfiguration->getParameter('pager');
        $requirePagination = $this
            ->getCurrentAction()
            ->isPaginationRequired();

        if ($pager == 'pagerfanta' && $requirePagination) {
            // adapter to pagerfanta
            $adapter = new PagerFantaAdminAdapter($this->dataProvider, $criteria, $orderBy);
            // create pager
            $this->pager = new Pagerfanta($adapter);
            $this->pager->setMaxPerPage($limit);
            $this->pager->setCurrentPage($offset);

            $entities = $this
                ->pager
                ->getCurrentPageResults();
        } else {
            // if the current action should retrieve only one entity, the offset should be zero
            if ($actionConfiguration->getParameter('load_strategy') !== AdminInterface::LOAD_STRATEGY_MULTIPLE) {
                $offset = 0;
            }
            $entities = $this
                ->dataProvider
                ->findBy($criteria, $orderBy, $limit, $offset);
        }
        if (!is_array($entities) && !($entities instanceof Collection)) {
            throw new Exception('The data provider should return either a collection or an array. Got '.gettype($entities).' instead');
        }

        if (is_array($entities)) {
            $entities = new ArrayCollection($entities);
        }
        $this->entities = $entities;
    }

    /**
     * Return loaded entities
     *
     * @return Collection
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
        if ($this->entities->count() == 0) {
            throw new Exception("Entity not found in admin \"{$this->getName()}\".");
        }
        if ($this->entities->count() > 1) {
            throw new Exception("Too much entities found in admin \"{$this->getName()}\".");
        }
        return $this->entities->first();
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
            /** @var ActionInterface $action */
            $action = $this->actions[$actionName];
            // checking roles permissions
            foreach ($roles as $role) {

                if ($role instanceof Role) {
                    $role = $role->getRole();
                }
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
     * @return integer[]
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
            throw new Exception(
                "Invalid action name \"{$name}\" for admin '{$this->getName()}'. Check your configuration"
            );
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
     * Return the current action or an exception if it is not set.
     *
     * @return ActionInterface
     * @throws Exception
     */
    public function getCurrentAction()
    {
        if ($this->currentAction === null) {
            // current action should be defined
            throw new Exception(
                'Current action is null. You should initialize it (with handleRequest method for example)'
            );
        }

        return $this->currentAction;
    }

    /**
     * Return if the current action has been initialized and set.
     *
     * @return boolean
     */
    public function isCurrentActionDefined()
    {
        return ($this->currentAction instanceof ActionInterface);
    }

    /**
     * Return admin configuration object.
     *
     * @return AdminConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Return a translation key for a message according to the Admin's translation pattern.
     *
     * @param string $message
     * @return string
     */
    protected function generateMessageTranslationKey($message)
    {
        return $this->getTranslationKey(
            $this->configuration->getParameter('translation_pattern'),
            $message,
            $this->name
        );
    }
}
