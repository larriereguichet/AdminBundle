<?php

namespace LAG\AdminBundle\Admin\Factory;

use Doctrine\ORM\EntityManager;
use LAG\AdminBundle\Action\Factory\ActionFactory;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\Factory\ConfigurationFactory;
use LAG\AdminBundle\DataProvider\DataProvider;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Event\AdminEvent;
use LAG\AdminBundle\Event\AdminFactoryEvent;
use Exception;
use LAG\AdminBundle\Message\MessageHandlerInterface;
use LAG\AdminBundle\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * AdminFactory.
 *
 * Create admin from configuration
 */
class AdminFactory
{
    /**
     * @var array
     */
    protected $admins = [];

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
     * AdminFactory constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param EntityManager $entityManager
     * @param ConfigurationFactory $configurationFactory
     * @param array $adminConfigurations
     * @param ActionFactory $actionFactory
     * @param MessageHandlerInterface $messageHandler
     */
    public function __construct(
        array $adminConfigurations,
        EventDispatcherInterface $eventDispatcher,
        EntityManager $entityManager,
        ConfigurationFactory $configurationFactory,        
        ActionFactory $actionFactory,
        MessageHandlerInterface $messageHandler
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
        $this->configurationFactory = $configurationFactory;
        $this->adminConfigurations = $adminConfigurations;
        $this->actionFactory = $actionFactory;
        $this->messageHandler = $messageHandler;
        $this->dataProviders = new ParameterBag();
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
        $event = new AdminFactoryEvent();
        $event->setAdminsConfiguration($this->adminConfigurations);

        // dispatch an event to allow configuration modification before resolving and creating admins
        $this
            ->eventDispatcher
            ->dispatch(AdminFactoryEvent::ADMIN_CREATION, $event);
        // set modified configuration
        $this->adminConfigurations = $event->getAdminsConfiguration();

        foreach ($this->adminConfigurations as $name => $configuration) {

            // dispatch an event to allow modification on this specific admin
            $event = new AdminEvent();
            $event
                ->setConfiguration($configuration)
                ->setAdminName($name)
            ;
            $this
                ->eventDispatcher
                ->dispatch(AdminEvent::ADMIN_CREATE, $event);

            // create Admin object
            $this->admins[$name] = $this->create($name, $event->getConfiguration());
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

        // create Admin object
        $admin = new Admin(
            $name,
            $dataProvider,
            $adminConfiguration,
            $this->messageHandler
        );

        // adding actions
        foreach ($adminConfiguration->getParameter('actions') as $actionName => $actionConfiguration) {
            // dispatching action create event for dynamic action creation
            $event = new AdminEvent();
            $event->setConfiguration($actionConfiguration);
            $event->setAdmin($admin);
            $event->setActionName($actionName);
            $this
                ->eventDispatcher
                ->dispatch(AdminEvent::ACTION_CREATE, $event);

            // creating action from configuration
            $action = $this
                ->actionFactory
                ->create($actionName, $event->getConfiguration(), $admin);

            // adding action to admin
            $admin->addAction($action);
        }
        return $admin;
    }

    /**
     * Return an admin from a Symfony request.
     *
     * @param Request $request
     * @return AdminInterface
     * @throws Exception
     */
    public function getAdminFromRequest(Request $request)
    {
        $routeParameters = $request->get('_route_params');

        if (!$routeParameters) {
            throw new Exception('Cannot find admin from request. _route_params parameters for request not found');
        }
        if (!array_key_exists('_admin', $routeParameters)) {
            throw new Exception('Cannot find admin from request. "_admin" route parameter is missing');
        }
        if (!array_key_exists('_action', $routeParameters)) {
            throw new Exception('Cannot find admin action from request. "_action" route parameter is missing');
        }
        $admin = $this->getAdmin($routeParameters['_admin']);

        return $admin;
    }

    /**
     * Return a admin by its name.
     *
     * @param $name
     * @return Admin
     * @throws Exception
     */
    public function getAdmin($name)
    {
        if (!array_key_exists($name, $this->admins)) {
            throw new Exception(sprintf('Admin with name "%s" not found. Check your admin configuration', $name));
        }

        return $this->admins[$name];
    }

    /**
     * Return all admins.
     *
     * @return AdminInterface[]
     */
    public function getAdmins()
    {
        return $this->admins;
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
