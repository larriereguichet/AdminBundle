<?php

namespace LAG\AdminBundle\Admin\Factory;

use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\AdminFactoryEvent;
use LAG\AdminBundle\Manager\GenericManager;
use BlueBear\BaseBundle\Behavior\ContainerTrait;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * AdminFactory.
 *
 * Create admin from configuration
 */
class AdminFactory
{
    const ADMIN_CREATION = 'lag.admin.adminCreation';

    use ContainerTrait;

    protected $admins = [];

    /**
     * @var ApplicationConfiguration
     */
    protected $applicationConfiguration;

    /**
     * Read configuration from container, then create admin with its actions and fields.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        // TODO remove dependence to container
        $this->container = $container;
        $admins = $this->container->getParameter('lag.admins');
        // dispatch an event with admins configurations to allow dynamic admin creation
        $event = new AdminFactoryEvent();
        $event->setAdminsConfiguration($admins);
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(self::ADMIN_CREATION, $event);

        // creating configured admin
        foreach ($admins as $adminName => $adminConfig) {
            $this->create($adminName, $adminConfig);
        }
    }

    /**
     * Return a loaded admin from a Symfony request.
     *
     * @param Request $request
     *
     * @return AdminInterface
     *
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
        $action = $admin->getAction($routeParameters['_action']);
        $admin->setCurrentAction($action);

        // set entity
        if ($action->getName() == 'list') {
            $entities = $admin->getManager()->findAll();
            $admin->setEntities($entities);
        } elseif ($action->getName() == 'edit') {
            $entity = $admin->getManager()->findOneBy([
                'id' => $request->get('id'),
            ]);
            $admin->setEntity($entity);
        }

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
     * Create an Admin from configuration values. It will be added to AdminFactory admin's list.
     *
     * @param $adminName
     * @param array $adminConfiguration
     */
    public function create($adminName, array $adminConfiguration)
    {
        $application = $this
            ->container
            ->get('lag.admin.application');
        $entityManager = $this
            ->container
            ->get('doctrine')
            ->getManager();
        $resolver = new OptionsResolver();
        // optional options
        $resolver->setDefaults([
            'actions' => [
                'list' => [],
                'create' => [],
                'edit' => [],
                'delete' => [],
            ],
            'manager' => null,
            'routing_url_pattern' => $application->getRoutingUrlPattern(),
            'routing_name_pattern' => $application->getRoutingNamePattern(),
            'controller' => 'LAGAdminBundle:Generic',
            'max_per_page' => $this
                ->container
                ->get('lag.admin.application')
                ->getMaxPerPage(),
        ]);
        // required options
        $resolver->setRequired([
            'entity',
            'form',
        ]);
        $adminConfiguration = $resolver->resolve($adminConfiguration);
        $adminConfig = new AdminConfiguration($adminConfiguration, $application);
        // gathering admin data
        /** @var EntityRepository $entityRepository */
        $entityRepository = $entityManager->getRepository($adminConfig->getEntityName());
        // create generic manager from configuration
        $entityManager = $this->createManagerFromConfig($adminConfig, $entityRepository);
        $admin = new Admin($adminName, $entityRepository, $entityManager, $adminConfig);
        // actions are optional
        if (!$adminConfig->getActions()) {
            // TODO move in default configuration
            $adminConfig->setActions([
                'list' => [],
                'create' => [],
                'edit' => [],
                'delete' => [],
            ]);
        }
        // TODO adding translation pattern for Admin
        $actionFactory = $this
            ->container
            ->get('lag.admin.action_factory');
        // adding actions
        foreach ($adminConfig->getActions() as $actionName => $actionConfig) {
            $action = $actionFactory->create($actionName, $actionConfig, $admin);
            $admin->addAction($action);
        }
        // adding admins to the pool
        $this->admins[$admin->getName()] = $admin;
    }

    /**
     * @return ApplicationConfiguration
     */
    public function getApplicationConfiguration()
    {
        return $this->applicationConfiguration;
    }

    /**
     * Create a generic manager from configuration.
     *
     * @param $adminConfig
     * @param EntityRepository $entityRepository
     *
     * @return GenericManager
     */
    protected function createManagerFromConfig(AdminConfiguration $adminConfig, EntityRepository $entityRepository)
    {
        $customManager = null;
        $methodsMapping = [];
        // set default entity manager
        /** @var EntityManager $entityManager */
        $entityManager = $this
            ->container
            ->get('doctrine')
            ->getManager();
        $managerConfiguration = $adminConfig->getManagerConfiguration();
        // custom manager is optional
        if (is_array($managerConfiguration)) {
            $customManager = $this->container->get($managerConfiguration['name']);

            if (array_key_exists('save', $managerConfiguration)) {
                $methodsMapping['save'] = $managerConfiguration['save'];
            }
            if (array_key_exists('list', $managerConfiguration)) {
                $methodsMapping['list'] = $managerConfiguration['list'];
            }
            if (array_key_exists('edit', $managerConfiguration)) {
                $methodsMapping['edit'] = $managerConfiguration['edit'];
            }
            if (array_key_exists('delete', $managerConfiguration)) {
                $methodsMapping['delete'] = $managerConfiguration['delete'];
            }
        }
        $manager = new GenericManager(
            $entityRepository,
            $entityManager,
            $customManager,
            $methodsMapping
        );

        return $manager;
    }
}
