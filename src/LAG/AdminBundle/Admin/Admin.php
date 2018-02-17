<?php

namespace LAG\AdminBundle\Admin;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Admin\Behaviors\AdminTrait;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use LAG\AdminBundle\Admin\Request\LoadParameterExtractor;
use LAG\AdminBundle\Admin\Request\RequestHandlerInterface;
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
     * @var ArrayCollection|Pagerfanta
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
     * @var RequestHandlerInterface
     */
    private $requestHandler;
    
    /**
     * Admin constructor.
     *
     * @param string                        $name
     * @param AdminConfiguration            $configuration
     * @param MessageHandlerInterface       $messageHandler
     * @param EventDispatcherInterface      $eventDispatcher
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface         $tokenStorage
     * @param RequestHandlerInterface       $requestHandler
     * @param ViewFactory                   $viewFactory
     * @param array                         $actions
     */
    public function __construct(
        $name,
        AdminConfiguration $configuration,
        MessageHandlerInterface $messageHandler,
        EventDispatcherInterface $eventDispatcher,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        RequestHandlerInterface $requestHandler,
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
        $this->viewFactory = $viewFactory;
        $this->requestHandler = $requestHandler;
    }
    
    /**
     * Load entities and set current action according to request and the optional filters.
     *
     * @param Request $request
     *
     * @throws Exception
     */
    public function handleRequest(Request $request)
    {
        $this->currentAction = $this->getAction($request);
        
        // Check if user is logged have required permissions to get current action
        $this->checkPermissions();
        
        // Get the current action configuration bag
        $actionConfiguration = $this
            ->currentAction
            ->getConfiguration()
        ;
    
        // If no loading is required, nothing left to do. Some actions do not require to load entities from
        // the DataProvider like create
        if (Admin::LOAD_STRATEGY_NONE === $actionConfiguration->getParameter('load_strategy')) {
            return;
        }

        // Handle the request for each configured form for the current action. If the form is not submitted, nothing
        // will happen
        $forms = $this->getCurrentAction()->getForms();

        foreach ($forms as $form) {
            $form->handleRequest($request);
        }
        
        // Retrieve the criteria to find one or more entities (from the request for sorting, pagination... and from
        // the filter form
        $filters = [];

        if (key_exists('filter_form', $forms) && $forms['filter_form']->isValid()) {
            $filters = $forms['filter_form']->getData();
        }
        $extractor = new LoadParameterExtractor($actionConfiguration, $filters);
        $extractor->load($request);
    
        // Load entities into the admin
        $this
            ->load(
                $extractor->getCriteria(),
                $extractor->getOrder(),
                $extractor->getMaxPerPage(),
                $extractor->getPage()
            )
        ;
    }
    
    /**
     * Check if the user is allowed to be here.
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
        
        // The user must be authenticated to access to an admin
        if (!($user instanceof UserInterface)) {
            throw new AccessDeniedException();
        }
        
        // Check if the current user is granted in Symfony's security configuration
        if (!$this->authorizationChecker->isGranted($user->getRoles(), $user)) {
            throw new AccessDeniedException();
        }
        $permissions = $this
            ->view
            ->getConfiguration()
            ->getParameter('permissions')
        ;
        
        // Check if the user is granted according to Admin configuration
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
        // Create an entity from the data provider
        $entity = $this
            ->entityLoader
            ->getDataProvider()
            ->create();
        
        // Add it to the collection
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
        // Inform the user that the entity is successfully saved
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
        // Inform the user that the entity is successfully removed
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
    public function load(array $criteria = [], array $orderBy = [], $limit = 25, $offset = 1)
    {
        // retrieve the data using the data provider via the entity loader
        $entities = $this
            ->entityLoader
            ->load($criteria, $orderBy, $limit, $offset)
        ;
        // the data provider should return an array or a collection of entities.
        if (!is_array($entities) && !$entities instanceof Collection && !$entities instanceof Pagerfanta) {
            throw new LogicException(
                'The data provider should return either a collection or an array. Got '.gettype($entities).' instead'
            );
        }

        // if an array is provided, transform it to a collection to be more convenient
        if (is_array($entities)) {
            $entities = new ArrayCollection($entities);
        }
        $this->view->setEntities($entities);
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

    /**
     * @return \LAG\AdminBundle\View\View
     */
    public function createView()
    {
        return $this
            ->viewFactory
            ->create(
                $this->getCurrentAction()->getName(),
                $this->name,
                $this->configuration,
                $this->getCurrentAction()->getConfiguration()
            )
        ;
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

    /**
     * @param Request $request
     *
     * @return ActionInterface
     *
     * @throws Exception
     */
    protected function getAction(Request $request)
    {
        if (!$this->requestHandler->supports($request)) {
            throw new BadRequestHttpException(
                'The given request can be processed. The route parameters "_admin" and "_action" probably missing'
            );
        }
        $actionName = $request->get('_route_params')[LAGAdminBundle::REQUEST_PARAMETER_ACTION];

        if (!key_exists($actionName, $this->actions)) {
            throw new Exception('Invalid action name "'.$actionName.'"');
        }

        return $this->actions[$actionName];
    }

    /**
     * Return true if all the submitted form in the request are valid.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->getCurrentAction()->isValid();
    }

    /**
     * Return the action set by the handleRequest().
     *
     * @return ActionInterface
     *
     * @throws Exception
     */
    public function getCurrentAction()
    {
        if (null === $this->currentAction) {
            throw new Exception(
                'The current action should be defined. Did you forget to call the handleRequest() method'
            );
        }

        return $this->currentAction;
    }
}
