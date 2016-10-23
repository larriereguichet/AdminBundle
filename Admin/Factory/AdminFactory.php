<?php

namespace LAG\AdminBundle\Admin\Factory;

use Doctrine\ORM\EntityManager;
use LAG\AdminBundle\Action\Factory\ActionFactory;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Event\AdminCreatedEvent;
use LAG\AdminBundle\Admin\Event\AdminCreateEvent;
use LAG\AdminBundle\Admin\Event\BeforeConfigurationEvent;
use LAG\AdminBundle\Admin\Registry\Registry;
use LAG\AdminBundle\Configuration\Factory\ConfigurationFactory;
use LAG\AdminBundle\DataProvider\DataProvider;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Admin\Event\AdminEvents;
use Exception;
use LAG\AdminBundle\Filter\Factory\RequestFilterFactory;
use LAG\AdminBundle\Message\MessageHandlerInterface;
use LAG\AdminBundle\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ConfigurationFactory
     */
    protected $configurationFactory;

    /**
     * User custom data provider, indexed by service id
     *
     * @var ParameterBagInterface
     */
    protected $dataProviders;

    /**
     * @var array
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
     * AdminFactory constructor.
     *
     * @param array $adminConfigurations
     * @param EventDispatcherInterface $eventDispatcher
     * @param EntityManager $entityManager
     * @param ConfigurationFactory $configurationFactory
     * @param ActionFactory $actionFactory
     * @param MessageHandlerInterface $messageHandler
     * @param Registry $registry
     * @param RequestFilterFactory $requestFilterFactory
     */
    public function __construct(
        array $adminConfigurations,
        EventDispatcherInterface $eventDispatcher,
        EntityManager $entityManager,
        ConfigurationFactory $configurationFactory,
        ActionFactory $actionFactory,
        MessageHandlerInterface $messageHandler,
        Registry $registry,
        RequestFilterFactory $requestFilterFactory
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
        $this->configurationFactory = $configurationFactory;
        $this->adminConfigurations = $adminConfigurations;
        $this->actionFactory = $actionFactory;
        $this->messageHandler = $messageHandler;
        $this->dataProviders = new ParameterBag();
        $this->registry = $registry;
        $this->requestFilterFactory = $requestFilterFactory;
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
     * Add user custom repositories (called in the repository compiler pass), to avoid injecting the service container
     *
     * @param string $name
     * @param DataProviderInterface $dataProvider
     */
    public function addDataProvider($name, DataProviderInterface $dataProvider)
    {
        $this
            ->dataProviders
            ->set($name, $dataProvider);
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
        // create or get repository according to the configuration
        if ($name) {
            // removing arobase if exists
            if (substr($name, 0, 1) == '@') {
                $name = substr($name, 1);
            }

            // custom data provider class must be loaded by the compiler pass
            if (!$this->dataProviders->has($name)) {
                throw new Exception(sprintf(
                    'Data provider %s not found. Did you add the @data_provider tag in your service ?',
                    $name
                ));
            }
            $dataProvider = $this
                ->dataProviders
                ->get($name);
        } else {
            // no data provider is configured, we create a new one
            $repository = $this
                ->entityManager
                ->getRepository($entityClass);

            if (!($repository instanceof RepositoryInterface)) {
                $repositoryClass = get_class($repository);
                throw new Exception("Repository {$repositoryClass} should implements ".RepositoryInterface::class);
            }
            $dataProvider = new DataProvider($repository);
        }
        return $dataProvider;
    }

}
