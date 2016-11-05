<?php

namespace LAG\AdminBundle\Admin\Factory;

use LAG\AdminBundle\Action\Factory\ActionFactory;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Admin\Event\AdminCreatedEvent;
use LAG\AdminBundle\Admin\Event\AdminCreateEvent;
use LAG\AdminBundle\Admin\Event\BeforeConfigurationEvent;
use LAG\AdminBundle\Admin\Registry\Registry;
use LAG\AdminBundle\Configuration\Factory\ConfigurationFactory;
use LAG\AdminBundle\DataProvider\DataProvider;
use LAG\AdminBundle\Admin\Event\AdminEvents;
use Exception;
use LAG\AdminBundle\DataProvider\Factory\DataProviderFactory;
use LAG\AdminBundle\Filter\Factory\RequestFilterFactory;
use LAG\AdminBundle\Message\MessageHandlerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * AdminFactory.
 *
 * Create admin from configuration
 */
class AdminFactory
{
    /**
     * @var bool
     */
    protected $isInit = false;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ConfigurationFactory
     */
    protected $configurationFactory;

    /**
     * @var AdminConfiguration[]
     */
    protected $adminConfigurations;

    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var MessageHandlerInterface
     */
    protected $messageHandler;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var RequestFilterFactory
     */
    protected $requestFilterFactory;

    /**
     * @var DataProviderFactory
     */
    protected $dataProviderFactory;

    /**
     * AdminFactory constructor.
     *
     * @param array $adminConfigurations
     * @param EventDispatcherInterface $eventDispatcher
     * @param ConfigurationFactory $configurationFactory
     * @param ActionFactory $actionFactory
     * @param MessageHandlerInterface $messageHandler
     * @param Registry $registry
     * @param RequestFilterFactory $requestFilterFactory
     * @param DataProviderFactory $dataProviderFactory
     */
    public function __construct(
        array $adminConfigurations,
        EventDispatcherInterface $eventDispatcher,
        ConfigurationFactory $configurationFactory,
        ActionFactory $actionFactory,
        MessageHandlerInterface $messageHandler,
        Registry $registry,
        RequestFilterFactory $requestFilterFactory,
        DataProviderFactory $dataProviderFactory
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->configurationFactory = $configurationFactory;
        $this->adminConfigurations = $adminConfigurations;
        $this->actionFactory = $actionFactory;
        $this->messageHandler = $messageHandler;
        $this->dataProviders = new ParameterBag();
        $this->registry = $registry;
        $this->requestFilterFactory = $requestFilterFactory;
        $this->dataProviderFactory = $dataProviderFactory;
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
            ->dispatch(AdminEvents::BEFORE_CONFIGURATION, $event);

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
                ->registry
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
     * @return Admin
     * @throws Exception
     */
    public function create($name, array $configuration)
    {
        // create AdminConfiguration object
        $adminConfiguration = $this
            ->configurationFactory
            ->createAdminConfiguration($configuration);

        // retrieve a data provider
        $dataProvider = $this->getDataProvider(
            $adminConfiguration->getParameter('entity'),
            $adminConfiguration->getParameter('data_provider')
        );

        // retrieve a request filter
        $requestFilter = $this
            ->requestFilterFactory
            ->create($adminConfiguration);

        // create Admin object
        $admin = new Admin(
            $name,
            $dataProvider,
            $adminConfiguration,
            $this->messageHandler,
            $this->eventDispatcher,
            $requestFilter
        );

        // adding actions
        foreach ($adminConfiguration->getParameter('actions') as $actionName => $actionConfiguration) {
            // create action and add it to the admin instance
            $this->createAction($admin, $actionName, $actionConfiguration);
        }

        return $admin;
    }

    /**
     * @return boolean
     */
    public function isInit()
    {
        return $this->isInit;
    }

    /**
     * @return Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * Create an Action from the configuration, and add it to the Admin.
     *
     * @param AdminInterface $admin
     * @param $actionName
     * @param array $actionConfiguration
     */
    protected function createAction(AdminInterface $admin, $actionName, array $actionConfiguration)
    {
        // creating action from configuration
        $action = $this
            ->actionFactory
            ->create($actionName, $actionConfiguration, $admin);

        // adding action to admin
        $admin->addAction($action);
    }

    /**
     * Return a configured data provider or create an new instance of the default one.
     *
     * @param string $entityClass
     * @param string|null $name
     * @return DataProvider|mixed
     * @throws Exception
     */
    protected function getDataProvider($entityClass, $name = null)
    {
        return $this
            ->dataProviderFactory
            ->get($entityClass, $name);
    }

}
