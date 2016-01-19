<?php

namespace LAG\AdminBundle\Admin\Factory;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Admin\Message\MessageHandler;
use LAG\AdminBundle\Event\AdminEvent;
use LAG\AdminBundle\Event\AdminFactoryEvent;
use Exception;
use LAG\AdminBundle\Repository\GenericRepository;
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
     * User custom repositories, indexed by service id
     *
     * @var ParameterBagInterface
     */
    protected $customRepositories;

    /**
     * @var array
     */
    protected $adminConfiguration;

    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var MessageHandler
     */
    protected $messageHandler;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param EntityManager $entityManager
     * @param ApplicationConfiguration $application
     * @param array $adminConfiguration
     * @param ActionFactory $actionFactory
     * @param MessageHandler $messageHandler
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        EntityManager $entityManager,
        ApplicationConfiguration $application,
        array $adminConfiguration,
        ActionFactory $actionFactory,
        MessageHandler $messageHandler
    )
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
        $this->application = $application;
        $this->adminConfiguration = $adminConfiguration;
        $this->actionFactory = $actionFactory;
        $this->messageHandler = $messageHandler;
        $this->customRepositories = new ParameterBag();
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
     * @param $name
     * @param array $configuration
     * @return Admin
     * @throws Exception
     */
    public function create($name, array $configuration)
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
            'repository' => null
        ]);
        // required options
        $resolver->setRequired([
            'entity',
            'form',
        ]);
        // resolve admin configuration
        $configuration = $resolver->resolve($configuration);
        // create AdminConfiguration object
        $classMetadata = $this
            ->entityManager
            ->getClassMetadata($configuration['entity']);
        $adminConfiguration = new AdminConfiguration($configuration, $classMetadata);

        // create or get repository accordinf to the configuration
        if ($adminConfiguration->getRepositoryServiceId()) {
            // custom repository class must be loaded by the compiler pass
            if ($this->customRepositories->has($adminConfiguration->getRepositoryServiceId())) {
                throw new Exception('Repository ' . $adminConfiguration->getRepositoryServiceId() . ' not found');
            }
            $repository = $this
                ->customRepositories
                ->get($adminConfiguration->getRepositoryServiceId());
        } else {
            // no repository configured, we create a new DoctrineRepository
            // first we get a doctrine default entity repository
            $doctrineRepository = new EntityRepository(
                $this->entityManager,
                $this->entityManager->getClassMetadata($adminConfiguration->getEntityName())
            );
            // then create a doctrine repository
            $repository = new GenericRepository(
                $this->entityManager,
                $doctrineRepository,
                $adminConfiguration->getEntityName()
            );
        }
        // create Admin object
        $admin = new Admin(
            $name,
            $this->entityManager,
            $repository,
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
     * Return a loaded admin from a Symfony request.
     *
     * @param Request $request
     * @param null $user
     *
     * @return AdminInterface
     * @throws Exception
     */
    public function getAdminFromRequest(Request $request, $user = null)
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
        $admin->handleRequest($request, $user);

        return $admin;
    }

    /**
     * Return a admin by its name.
     *
     * @param $name
     *
     * @return Admin
     *
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
     * @param string $serviceId
     * @param object $repository
     */
    public function addRepository($serviceId, $repository)
    {
        $this
            ->customRepositories
            ->set($serviceId, $repository);
    }
}
