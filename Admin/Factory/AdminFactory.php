<?php

namespace LAG\AdminBundle\Admin\Factory;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Admin\ManagerInterface;
use LAG\AdminBundle\Admin\Message\MessageHandler;
use LAG\AdminBundle\Event\AdminEvent;
use LAG\AdminBundle\Event\AdminFactoryEvent;
use LAG\AdminBundle\Manager\GenericManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
     * @var ManagerInterface[]
     */
    protected $customManagers = [];

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
            'controller' => 'LAGAdminBundle:Generic',
            'max_per_page' => $this->application->getMaxPerPage()
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
        $repository = $this
            ->entityManager
            ->getRepository($adminConfiguration->getEntityName());
        // create generic manager from configuration
        $entityManager = $this->createManagerFromConfig($name, $adminConfiguration, $repository, $this->entityManager);

        $admin = new Admin(
            $name,
            $repository,
            $entityManager,
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
     * Create a generic manager from configuration.
     *
     * @param $adminName
     * @param AdminConfiguration $adminConfig
     * @param ObjectRepository $repository
     * @param ObjectManager $entityManager
     * @return GenericManager
     * @throws Exception
     */
    protected function createManagerFromConfig($adminName, AdminConfiguration $adminConfig, ObjectRepository $repository, ObjectManager $entityManager)
    {
        $managerClass = $adminConfig->getManager();

        if (class_exists($managerClass)) {
            $manager = new $managerClass($entityManager, $repository);
        } elseif (isset($this->customManagers[$managerClass])) {
            $manager = $this->customManagers[$managerClass];
        } else {
            throw new Exception("Unable to find manager '{$managerClass}' for Admin '{$adminName}'");
        }
        if (!($manager instanceof ManagerInterface)) {
            throw new Exception("Manager '{$managerClass}' should implements ManagerInterface");
        }
        return $manager;
    }

    /**
     * @param string $key
     * @param ManagerInterface $manager
     */
    public function addCustomManager($key, ManagerInterface $manager)
    {
        $this->customManagers[$key] = $manager;
    }
}
