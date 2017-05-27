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
use LAG\AdminBundle\Configuration\Factory\ConfigurationFactory;
use LAG\AdminBundle\Admin\Event\AdminEvents;
use LAG\AdminBundle\Doctrine\Repository\DoctrineRepositoryFactory;
use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Message\MessageHandlerInterface;
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
     * @var DoctrineRepositoryFactory
     */
    private $doctrineRepositoryFactory;
    
    /**
     * AdminFactory constructor.
     *
     * @param array                         $adminConfigurations
     * @param EventDispatcherInterface      $eventDispatcher
     * @param MessageHandlerInterface       $messageHandler
     * @param Registry                      $adminRegistry
     * @param ActionRegistry                $actionRegistry
     * @param ActionFactory                 $actionFactory
     * @param ConfigurationFactory          $configurationFactory
     * @param DoctrineRepositoryFactory     $doctrineRepositoryFactory
     * @param RequestHandler                $requestHandler
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface         $tokenStorage
     */
    public function __construct(
        array $adminConfigurations,
        EventDispatcherInterface $eventDispatcher,
        MessageHandlerInterface $messageHandler,
        Registry $adminRegistry,
        ActionRegistry $actionRegistry,
        ActionFactory $actionFactory,
        ConfigurationFactory $configurationFactory,
        DoctrineRepositoryFactory $doctrineRepositoryFactory,
        RequestHandler $requestHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->configurationFactory = $configurationFactory;
        $this->adminConfigurations = $adminConfigurations;
        $this->actionFactory = $actionFactory;
        $this->messageHandler = $messageHandler;
        $this->adminRegistry = $adminRegistry;
        $this->requestHandler = $requestHandler;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->actionRegistry = $actionRegistry;
        $this->doctrineRepositoryFactory = $doctrineRepositoryFactory;
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
                ->dispatch(AdminEvents::ADMIN_CREATE, $event);

            // create Admin object and add it to the registry
            $admin = $this->create($name, $event->getAdminConfiguration());
            $this
                ->adminRegistry
                ->add($admin);

            // dispatch post-create event
            $event = new AdminCreatedEvent($admin);
            $this
                ->eventDispatcher
                ->dispatch(AdminEvents::ADMIN_CREATED, $event);
        }
        $this->isInit = true;
    }

    /**
     * Create an Admin from configuration values. It will be added to AdminFactory admin's list.
     *
     * @param string $name
     * @param array $configuration
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
            ->createAdminConfiguration($configuration)
        ;
    
        if (null !== $adminConfiguration->getParameter('repository')) {
            // if a repository service id configured, we retrieve this repository from the factory
            $repository = $this
                ->doctrineRepositoryFactory
                ->get($adminConfiguration->getParameter('repository'))
            ;
        } else {
            // if no repository is configured, we create one from the Doctrine repository
            $repository = $this
                ->doctrineRepositoryFactory
                ->create($adminConfiguration->getParameter('entity'))
            ;
        }
    
        if (null === $repository) {
            throw new LogicException('Unable to find a repository for admin "'.$name.'"');
        }
        
        // retrieve Admin dynamic class
        $adminClass = $this
            ->configurationFactory
            ->getApplicationConfiguration()
            ->getParameter('admin_class')
        ;
    
        // retrieve the actions services
        $actions = $this->retrieveActions($name, $configuration);

        // create Admin object
        $admin = new $adminClass(
            $name,
            $repository,
            $adminConfiguration,
            $this->messageHandler,
            $this->eventDispatcher,
            $this->authorizationChecker,
            $this->tokenStorage,
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
    
    /**
     * @param $adminName
     * @param array $configuration
     *
     * @return array
     */
    private function retrieveActions($adminName, array $configuration)
    {
        $actions = [];
    
        foreach ($configuration['actions'] as $name => $actionConfiguration) {
            if (null !== $actionConfiguration && array_key_exists('service', $actionConfiguration)) {
                $serviceId = $actionConfiguration['service'];
            } else {
                $serviceId = $this->getActionServiceId($name, $adminName);
            }
            
            $actions[$name] = $this
                ->actionRegistry
                ->get($serviceId)
            ;
        }
    
        return $actions;
    }
    
    /**
     * @param $name
     * @param $adminName
     *
     * @return string
     */
    private function getActionServiceId($name, $adminName)
    {
        $mapping = LAGAdminBundle::getDefaultActionServiceMapping();
    
        if (!array_key_exists($name, $mapping)) {
            throw new LogicException('Action "'.$name.'" service id was not found for admin "'.$adminName.'"');
        }
    
        return $mapping[$name];
    }
}
