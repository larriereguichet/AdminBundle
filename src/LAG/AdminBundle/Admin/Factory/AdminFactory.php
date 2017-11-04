<?php

namespace LAG\AdminBundle\Admin\Factory;

use LAG\AdminBundle\Action\Factory\ActionFactory;
use LAG\AdminBundle\Action\Registry\Registry as ActionRegistry;
use LAG\AdminBundle\Admin\AdminAwareInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Admin\Event\AdminCreatedEvent;
use LAG\AdminBundle\Admin\Event\AdminCreateEvent;
use LAG\AdminBundle\Admin\Event\AdminInjectedEvent;
use LAG\AdminBundle\Admin\Event\BeforeConfigurationEvent;
use LAG\AdminBundle\Admin\Registry\Registry;
use LAG\AdminBundle\Admin\Request\RequestHandler;
use LAG\AdminBundle\Admin\Event\AdminEvents;
use LAG\AdminBundle\Application\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\DataProvider\Factory\DataProviderFactory;
use LAG\AdminBundle\DataProvider\Loader\EntityLoader;
use LAG\AdminBundle\Message\MessageHandlerInterface;
use LAG\AdminBundle\View\Factory\ViewFactory;
use LogicException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * AdminFactory.
 *
 * This class is responsible for the creation of Admins from configuration array.
 */
class AdminFactory
{
    /**
     * @var bool
     */
    private $isInit = false;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * @var AdminConfiguration[]
     */
    private $adminConfigurations;

    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var MessageHandlerInterface
     */
    private $messageHandler;

    /**
     * @var Registry
     */
    private $adminRegistry;

    /**
     * @var DataProviderFactory
     */
    private $dataProviderFactory;
    
    /**
     * @var RequestHandler
     */
    private $requestHandler;
    
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    
    /**
     * @var ActionRegistry
     */
    private $actionRegistry;
    
    /**
     * @var ViewFactory
     */
    private $viewFactory;
    
    /**
     * @var ApplicationConfigurationStorage
     */
    private $applicationConfigurationStorage;
    
    /**
     * AdminFactory constructor.
     *
     * @param array                           $adminConfigurations
     * @param EventDispatcherInterface        $eventDispatcher
     * @param MessageHandlerInterface         $messageHandler
     * @param Registry                        $adminRegistry
     * @param ActionRegistry                  $actionRegistry
     * @param ActionFactory                   $actionFactory
     * @param ConfigurationFactory            $configurationFactory
     * @param DataProviderFactory             $dataProviderFactory
     * @param ViewFactory                     $viewFactory
     * @param RequestHandler                  $requestHandler
     * @param AuthorizationCheckerInterface   $authorizationChecker
     * @param TokenStorageInterface           $tokenStorage
     * @param ApplicationConfigurationStorage $applicationConfigurationStorage
     */
    public function __construct(
        array $adminConfigurations,
        EventDispatcherInterface $eventDispatcher,
        MessageHandlerInterface $messageHandler,
        Registry $adminRegistry,
        ActionRegistry $actionRegistry,
        ActionFactory $actionFactory,
        ConfigurationFactory $configurationFactory,
        DataProviderFactory $dataProviderFactory,
        ViewFactory $viewFactory,
        RequestHandler $requestHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        ApplicationConfigurationStorage $applicationConfigurationStorage
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->configurationFactory = $configurationFactory;
        $this->adminConfigurations = $adminConfigurations;
        $this->actionFactory = $actionFactory;
        $this->messageHandler = $messageHandler;
        $this->adminRegistry = $adminRegistry;
        $this->dataProviderFactory = $dataProviderFactory;
        $this->requestHandler = $requestHandler;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->actionRegistry = $actionRegistry;
        $this->viewFactory = $viewFactory;
        $this->applicationConfigurationStorage = $applicationConfigurationStorage;
    }

    /**
     * Create admins from configuration and load them into the pool. Dispatch ADMIN_CREATE event.
     */
    public function init()
    {
        // init only once
        if ($this->isInit) {
            return;
        }
        // dispatch an event to allow configuration modification before resolving and creating admins
        $event = new BeforeConfigurationEvent($this->adminConfigurations);
        $this
            ->eventDispatcher
            ->dispatch(AdminEvents::BEFORE_CONFIGURATION, $event)
        ;

        // get the modified configuration
        $this->adminConfigurations = $event->getAdminConfigurations();
        
        // create Admins according to the given configuration
        foreach ($this->adminConfigurations as $name => $configuration) {
            // dispatch an event to allow modification on a specific admin
            $event = new AdminCreateEvent($name, $configuration);
            $this
                ->eventDispatcher
                ->dispatch(AdminEvents::ADMIN_CREATE, $event)
            ;

            // create Admin object and add it to the registry
            $admin = $this->create($name, $event->getAdminConfiguration());
            $this
                ->adminRegistry
                ->add($admin)
            ;

            // dispatch post-create event
            $event = new AdminCreatedEvent($admin);
            $this
                ->eventDispatcher
                ->dispatch(AdminEvents::ADMIN_CREATED, $event)
            ;
        }
        $this->isInit = true;
    }

    /**
     * Create an Admin from configuration values. It will be added to AdminFactory admin's list.
     *
     * @param string $name
     * @param array  $configuration
     *
     * @return AdminInterface
     *
     * @throws LogicException
     */
    public function create($name, array $configuration)
    {
        // create AdminConfiguration object
        $adminConfiguration = $this
            ->configurationFactory
            ->create($configuration)
        ;
        
        // retrieve a data provider and load it into the loader
        $dataProvider = $this
            ->dataProviderFactory
            ->get($adminConfiguration->getParameter('data_provider'), $adminConfiguration->getParameter('entity'))
        ;
        $entityLoader = new EntityLoader($dataProvider);
    
        // retrieve Admin dynamic class
        $adminClass = $this
            ->applicationConfigurationStorage
            ->getApplicationConfiguration()
            ->getParameter('admin_class')
        ;
    
        // retrieve the actions services
        $actions = $this
            ->actionFactory
            ->getActions($name, $configuration)
        ;

        // create Admin object
        $admin = new $adminClass(
            $name,
            $entityLoader,
            $adminConfiguration,
            $this->messageHandler,
            $this->eventDispatcher,
            $this->authorizationChecker,
            $this->tokenStorage,
            $this->requestHandler,
            $this->viewFactory,
            $actions
        );
    
        if (!$admin instanceof AdminInterface) {
            throw new LogicException('Class "'.get_class($admin).'" should implements '.AdminInterface::class);
        }

        return $admin;
    }
    
    /**
     * Inject an Admin into an AdminAware controller. Add this Admin to Twig global parameters.
     *
     * @param mixed $controller
     * @param Request $request
     */
    public function injectAdmin($controller, Request $request)
    {
        // the controller should be Admin aware to have an Admin injected
        if (!$controller instanceof AdminAwareInterface) {
            return;
        }
        // get admin from the request parameters
        $admin = $this
            ->requestHandler
            ->handle($request)
        ;
        
        // inject the Admin in the controller
        $controller->setAdmin($admin);
        
        // dispatch an even to allow some custom logic
        $this
            ->eventDispatcher
            ->dispatch(AdminEvents::ADMIN_INJECTED, new AdminInjectedEvent($admin, $controller))
        ;
    }

    /**
     * Return true if the AdminFactory is initialized.
     *
     * @return boolean
     */
    public function isInit()
    {
        return $this->isInit;
    }
}
