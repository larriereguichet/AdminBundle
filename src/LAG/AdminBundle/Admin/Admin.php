<?php

namespace LAG\AdminBundle\Admin;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Admin\Behaviors\AdminTrait;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use LAG\AdminBundle\Admin\Request\LoadParameterExtractor;
use LAG\AdminBundle\Admin\Request\RequestHandler;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\DataProvider\Loader\EntityLoaderInterface;
use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Message\MessageHandlerInterface;
use LAG\AdminBundle\View\Factory\ViewFactory;
use LAG\AdminBundle\View\ViewInterface;
use LogicException;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
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
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;
    
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;
    
    /**
     * @var FormInterface|null
     */
    protected $filterForm;
    
    /**
     * @var EntityLoaderInterface
     */
    protected $entityLoader;
    
    /**
     * @var ViewFactory
     */
    private $viewFactory;
    
    /**
     * @var ViewInterface
     */
    private $view;
    /**
     * @var RequestHandler
     */
    private $requestHandler;
    
    /**
     * Admin constructor.
     *
     * @param string                        $name
     * @param EntityLoaderInterface         $entityLoader
     * @param AdminConfiguration            $configuration
     * @param MessageHandlerInterface       $messageHandler
     * @param EventDispatcherInterface      $eventDispatcher
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface         $tokenStorage
     * @param RequestHandler                $requestHandler
     * @param ViewFactory                   $viewFactory
     * @param array                         $actions
     */
    public function __construct(
        $name,
        EntityLoaderInterface $entityLoader,
        AdminConfiguration $configuration,
        MessageHandlerInterface $messageHandler,
        EventDispatcherInterface $eventDispatcher,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        RequestHandler $requestHandler,
        ViewFactory $viewFactory,
        $actions = []
    ) {
        $this->name = $name;
        $this->entities = new ArrayCollection();
        $this->configuration = $configuration;
        $this->messageHandler = $messageHandler;
        $this->eventDispatcher = $eventDispatcher;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->actions = $actions;
        $this->entityLoader = $entityLoader;
        $this->dataProvider = $entityLoader->getDataProvider();
        $this->viewFactory = $viewFactory;
        $this->requestHandler = $requestHandler;
    }
    
    /**
     * Load entities and set current action according to request and the optional filters.
     *
     * @param Request $request
     * @param array   $filters
     */
    public function handleRequest(Request $request, array $filters = [])
    {
        if (!$this->requestHandler->supports($request)) {
            throw new BadRequestHttpException(
                'The given request can be processed. The route parameters "_admin" and "_action" probably missing'
            );
        }
        $actionName = $request->get('_route_params')[LAGAdminBundle::REQUEST_PARAMETER_ACTION];
    
        $this->view = $this
            ->viewFactory
            ->create($actionName, $this->name, $this->configuration, $this->configuration->getParameter('actions')[$actionName])
        ;
        
        // set current action
//        $this->currentAction = $this->getAction(
//            $request->get('_route_params')[LAGAdminBundle::REQUEST_PARAMETER_ACTION]
//        );
        
        // check if user is logged have required permissions to get current action
        $this->checkPermissions();
        
        // get the current action configuration bag
        $actionConfiguration = $this
            ->view
            ->getConfiguration()
        ;
    
        // if no loading is required, no more thing to do. Some actions do not require to load entities from
        // the DataProvider
        if (Admin::LOAD_STRATEGY_NONE === $actionConfiguration->getParameter('load_strategy')) {
            return;
        }
        
        // retrieve the criteria to find one or more entities (from the request for sorting, pagination... and from
        // the filter form
        $loader = new LoadParameterExtractor($actionConfiguration, $filters);
        $loader->load($request);
    
        // load entities according to action and request
        $this
            ->entityLoader
            ->configure($actionConfiguration)
        ;
        
        $this
            ->load(
                $loader->getCriteria(),
                $loader->getOrder(),
                $loader->getMaxPerPage(),
                $loader->getPage()
            )
        ;
        $this->view->setEntities($this->entities);
    }
    
    /**
     * Check if user is allowed to be here.
     *
     * @throws LogicException|AccessDeniedException
     */
    public function checkPermissions()
    {
        $user = $this
            ->tokenStorage
            ->getToken()
            ->getUser()
        ;
        
        // must be authenticated to access to an admin
        if (!($user instanceof UserInterface)) {
            throw new AccessDeniedException();
        }
        
        // a view must have been defined
        if (null === $this->view) {
            throw new LogicException(
                'A view must be defined before checking the permissions. Maybe you forget to call handleRequest()'
            );
        }
        // check if the current User is granted in Symfony security configuration
        if (!$this->authorizationChecker->isGranted($user->getRoles(), $user)) {
            throw new AccessDeniedException();
        }
        $permissions = $this
            ->view
            ->getConfiguration()
            ->getParameter('permissions')
        ;
        
        // check if the User is granted according to Admin configuration
        if (!$this->authorizationChecker->isGranted($permissions, $user)) {
            throw new AccessDeniedException();
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
     * Save entity via admin manager.
     */
    public function save()
    {
        foreach ($this->entities as $entity) {
            $this
                ->dataProvider
                ->save($entity)
            ;
        }
        // inform the user that the entity is saved
        $this
            ->messageHandler
            ->handleSuccess($this->generateMessageTranslationKey('saved'))
        ;
    }
    
    /**
     * Remove an entity with data provider.
     */
    public function remove()
    {
        foreach ($this->entities as $entity) {
            $this
                ->dataProvider
                ->remove($entity);
        }
        // inform the user that the entity is removed
        $this
            ->messageHandler
            ->handleSuccess($this->generateMessageTranslationKey('deleted'))
        ;
    }
    
    /**
     * Return the number of entities managed by the Admin.
     *
     * @return int
     */
    public function count()
    {
        $count = $this
            ->dataProvider
            ->count()
        ;
    
        if (!is_integer($count)) {
            throw new LogicException(
                'The data provider should return an integer for the "count()" method, given : '.gettype($count)
            );
        }
    
        return $count;
    }
    
    /**
     * Load entities according to the given criteria and the current action configuration.
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int   $limit
     * @param int   $offset
     */
    public function load(array $criteria, array $orderBy = [], $limit = 25, $offset = 1)
    {
        // retrieve the data using the data provider via the entity loader
        $entities = $this
            ->entityLoader
            ->load($criteria, $orderBy, $limit, $offset)
        ;
    
        // either, we have an instance of Pagerfanta, either we should have an array or a collection
        if ($entities instanceof Pagerfanta) {
            // if the entities are inside a pager, we get the result and we set the pager for the view
            $this->entities = new ArrayCollection($entities->getCurrentPageResults());
            $this->pager = $entities;
        } else {
            // the data provider should return an array or a collection of entities.
            if (!is_array($entities) && !$entities instanceof Collection) {
                throw new LogicException(
                    'The data provider should return either a collection or an array. Got '.gettype($entities).' instead'
                );
            }
    
            // if an array is provided, transform it to a collection to be more convenient
            if (is_array($entities)) {
                $entities = new ArrayCollection($entities);
            }
            $this->entities = $entities;
        }
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
            throw new Exception('Entity not found in admin "'.$this->getName().'""');
        }
        
        if ($this->entities->count() > 1) {
            throw new Exception(
                'Too much entities found in admin '.$this->getName().' ('.$this->entities->count().' entities found, '
                .'expected one). Check the load strategy configuration'
            );
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
     * Return true if the Action with name $name exists in the Admin. If the method return true, it does not necessarily
     * means that the action is allowed in the current context.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasAction($name)
    {
        $actions = $this->configuration->getParameter('actions');
        
        if (!key_exists($name, $actions)) {
            return false;
        }
    
        return null !== $actions[$name] && false !== $actions[$name];
    }
    
    public function getView()
    {
        if (!$this->hasView()) {
            throw new LogicException('The view is not defined. Maybe you forgot to call handleRequest() ?');
        }
    
        return $this->view;
    }
    
    public function hasView()
    {
        return null !== $this->view;
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
     * Return the filter form if it was initialized.
     *
     * @return FormInterface
     *
     * @throws Exception
     */
    public function getFilterForm()
    {
        if (null === $this->filterForm) {
            throw new Exception(
                'The filter form is null. Check you have configured filters. You should initialize the filter form 
                (with $admin->handleRequest() method for example)'
            );
        }
        
        return $this->filterForm;
    }
    
    /**
     * Return true if the filter form has been set.
     *
     * @return bool
     */
    public function hasFilterForm()
    {
        return null !== $this->filterForm;
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
