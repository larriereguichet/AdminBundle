<?php

namespace LAG\AdminBundle\Admin\Factory;

use Doctrine\ORM\EntityManager;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\DataProvider\DataProvider;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Event\AdminEvent;
use LAG\AdminBundle\Event\AdminFactoryEvent;
use Exception;
use LAG\AdminBundle\Message\MessageHandlerInterface;
use LAG\DoctrineRepositoryBundle\Repository\DoctrineRepository;
use LAG\DoctrineRepositoryBundle\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
     * @var ApplicationConfiguration
     */
    protected $application;

    /**
     * User custom data provider, indexed by service id
     *
     * @var ParameterBagInterface
     */
    protected $dataProviders;

    /**
     * @var array
     */
    protected $adminConfiguration;

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
     * @param ApplicationConfiguration $application
     * @param array $adminConfiguration
     * @param ActionFactory $actionFactory
     * @param MessageHandlerInterface $messageHandler
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        EntityManager $entityManager,
        ApplicationConfiguration $application,
        array $adminConfiguration,
        ActionFactory $actionFactory,
        MessageHandlerInterface $messageHandler
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
        $this->application = $application;
        $this->adminConfiguration = $adminConfiguration;
        $this->actionFactory = $actionFactory;
        $this->messageHandler = $messageHandler;
        $this->dataProviders = new ParameterBag();
    }

    /**
     * Create admins from configuration and load them into the pool. Dispatch ADMIN_CREATE event.
     */
    public function init()
    {
        if ($this->isInit) {
            return;
        }
        $event = new AdminFactoryEvent();
        $event->setAdminsConfiguration($this->adminConfiguration);
        $this->eventDispatcher->dispatch(AdminFactoryEvent::ADMIN_CREATION, $event);
        $this->adminConfiguration = $event->getAdminsConfiguration();

        foreach ($this->adminConfiguration as $name => $configuration) {
            $event = new AdminEvent();
            $event->setConfiguration($configuration);
            $this->eventDispatcher->dispatch(AdminEvent::ADMIN_CREATE, $event);
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
        // resolve admin configuration
        $configuration = $this->resolveConfiguration($configuration);
        // retrieve metadata from entity manager
        $configuration['metadata'] = $this
            ->entityManager
            ->getClassMetadata($configuration['entity']);
        // create AdminConfiguration object
        $adminConfiguration = new AdminConfiguration($configuration);

        // retrieve a data provider
        $dataProvider = $this->getDataProvider(
            $adminConfiguration->getEntityName(),
            $adminConfiguration->getDataProvider()
        );

        // create Admin object
        $admin = new Admin(
            $name,
            $dataProvider,
            $adminConfiguration,
            $this->messageHandler
        );
        // adding actions
        foreach ($adminConfiguration->getActions() as $actionName => $actionConfiguration) {
            // dispatching action create event for dynamic action creation
            $event = new AdminEvent();
            $event->setConfiguration($actionConfiguration);
            $event->setAdmin($admin);
            $event->setActionName($actionName);
            $this->eventDispatcher->dispatch(AdminEvent::ACTION_CREATE, $event);
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
            throw new Exception('Cannot find admin _route_params parameters for request');
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
     * @return Admin[]
     */
    public function getAdmins()
    {
        return $this->admins;
    }

    /**
     * Add user custom repositories (for the repository compiler pass), to avoid injecting the service container
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
                    'Data provider %s not found. Did you add the @data_provider tag in your service',
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
                throw new Exception("Repository {$repositoryClass} should implements " . RepositoryInterface::class);
            }

            $dataProvider = new DataProvider($repository);
        }
        return $dataProvider;
    }

    /**
     * Resolve admin configuration.
     *
     * @param array $configuration
     * @return array
     */
    protected function resolveConfiguration(array $configuration)
    {
        $resolver = new OptionsResolver();
        // optional options
        $resolver->setDefaults([
            'actions' => [
                'list' => [],
                'create' => [],
                'edit' => [],
                'delete' => [],
            ],
            'batch' => true,
            'manager' => 'LAG\AdminBundle\Manager\GenericManager',
            'routing_url_pattern' => $this->application->getRoutingUrlPattern(),
            'routing_name_pattern' => $this->application->getRoutingNamePattern(),
            'controller' => 'LAGAdminBundle:CRUD',
            'max_per_page' => $this->application->getMaxPerPage(),
            'data_provider' => null
        ]);
        // required options
        $resolver->setRequired([
            'entity',
            'form',
        ]);

        return $resolver->resolve($configuration);
    }
}
