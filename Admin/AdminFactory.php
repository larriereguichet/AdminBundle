<?php

namespace BlueBear\AdminBundle\Admin;

use BlueBear\AdminBundle\Admin\Application\ApplicationConfiguration;
use BlueBear\AdminBundle\Event\AdminFactoryEvent;
use BlueBear\AdminBundle\Manager\GenericManager;
use BlueBear\BaseBundle\Behavior\ContainerTrait;
use BlueBear\BaseBundle\Behavior\StringUtilsTrait;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * AdminFactory
 *
 * Create admin from configuration
 */
class AdminFactory
{
    const ADMIN_CREATION = 'bluebear.admin.adminCreation';

    use StringUtilsTrait, ContainerTrait;

    protected $admins = [];

    /**
     * @var ApplicationConfiguration
     */
    protected $applicationConfiguration;

    /**
     * Read configuration from container, then create admin with its actions and fields
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $admins = $this->getContainer()->getParameter('bluebear.admins');
        // dispatch an event with admins configurations to allow dynamic admin creation
        $event = new AdminFactoryEvent();
        $event->setAdminsConfiguration($admins);
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(self::ADMIN_CREATION, $event);

        // creating configured admin
        foreach ($admins as $adminName => $adminConfig) {
            $this->createAdminFromConfig($adminName, $adminConfig);
        }
    }

    /**
     * Return a loaded admin from a Symfony request
     *
     * @param Request $request
     * @return Admin
     * @throws Exception
     */
    public function getAdminFromRequest(Request $request)
    {
        $routeParameters = $request->get('_route_params');

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
        } else if ($action->getName() == 'edit') {
            $entity = $admin->getManager()->findOneBy([
                'id' => $request->get('id')
            ]);
            $admin->setEntity($entity);
        }
        return $admin;
    }

    /**
     * Return a admin by its name
     *
     * @param $name
     * @return Admin
     * @throws Exception
     */
    public function getAdmin($name)
    {
        if (!array_key_exists($name, $this->admins)) {
            throw new Exception('Invalid admin name "' . $name . '". Did you add it in your configuration ?');
        }
        return $this->admins[$name];
    }

    /**
     * Return all admins
     *
     * @return Admin[]
     */
    public function getAdmins()
    {
        return $this->admins;
    }

    /**
     * Create an Admin from configuration values. It will be added to AdminFactory admin's list
     *
     * @param $adminName
     * @param array $adminConfigArray
     */
    public function createAdminFromConfig($adminName, array $adminConfigArray)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        // gathering admin data
        $adminConfig = new AdminConfig();
        $adminConfig->hydrateFromConfiguration($adminConfigArray, $this->getContainer()->getParameter('bluebear.admin.application'));
        $entityRepository = $entityManager->getRepository($adminConfig->entityName);
        // create generic manager from configuration
        $entityManager = $this->createManagerFromConfig($adminConfig, $entityRepository);
        $admin = new Admin($adminName, $entityRepository, $entityManager, $adminConfig);
        // actions are optional
        if (!$adminConfig->actions) {
            $adminConfig->actions = [
                'list' => [],
                'create' => [],
                'edit' => [],
                'delete' => []
            ];
        }
        // adding actions
        foreach ($adminConfig->actions as $actionName => $actionConfig) {
            $admin->addAction($this->createActionFromConfig($actionName, $actionConfig, $admin));
        }
        // adding admins to the pool
        $this->admins[$admin->getName()] = $admin;
    }

    /**
     * Create application configuration from request and application parameters
     *
     * @param array $applicationConfiguration
     * @return ApplicationConfiguration
     */
    public function createApplicationFromConfiguration(array $applicationConfiguration)
    {
        $applicationConfiguration = array_merge($this->getDefaultApplicationConfiguration(), $applicationConfiguration);
        $applicationConfig = new ApplicationConfiguration();
        $applicationConfig->hydrateFromConfiguration($applicationConfiguration);

        return $applicationConfig;
    }

    /**
     * Create an Action from configuration values
     *
     * @param $actionName
     * @param $actionConfiguration
     * @param Admin $admin
     * @return Action
     */
    protected function createActionFromConfig($actionName, $actionConfiguration, Admin $admin)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults($this->getDefaultActionConfiguration($actionName));
        $actionConfiguration = $resolver->resolve($actionConfiguration);

        // guess title if not provided
        if (!$actionConfiguration['title']) {
            $actionConfiguration['title'] = $this->getDefaultActionTitle($admin->getName(), $actionName);
        }
        $action = new Action();
        $action->setName($actionName);
        $action->setTitle($actionConfiguration['title']);
        $action->setPermissions($actionConfiguration['permissions']);
        $action->setRoute($admin->generateRouteName($action->getName()));
        $action->setExport($actionConfiguration['export']);

        // adding fields items to actions
        foreach ($actionConfiguration['fields'] as $fieldName => $fieldConfiguration) {
            $field = new Field();
            $field->setName($fieldName);
            $field->setTitle($this->inflectString($fieldName));

            if (array_key_exists('length', $fieldConfiguration)) {
                $field->setLength($fieldConfiguration['length']);
            }
            $action->addField($field);
        }
        return $action;
    }

    /**
     * Create a generic manager from configuration
     *
     * @param $adminConfig
     * @param EntityRepository $entityRepository
     * @return GenericManager
     */
    protected function createManagerFromConfig(AdminConfig $adminConfig, EntityRepository $entityRepository)
    {
        $customManager = null;
        $methodsMapping = [];
        // set default entity manager
        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        // custom manager is optional
        if ($adminConfig->managerConfiguration) {
            $customManager = $this->getContainer()->get($adminConfig->managerConfiguration['name']);

            if (array_key_exists('save', $adminConfig->managerConfiguration)) {
                $methodsMapping['save'] = $adminConfig->managerConfiguration['save'];
            }
            if (array_key_exists('list', $adminConfig->managerConfiguration)) {
                $methodsMapping['list'] = $adminConfig->managerConfiguration['list'];
            }
            if (array_key_exists('edit', $adminConfig->managerConfiguration)) {
                $methodsMapping['edit'] = $adminConfig->managerConfiguration['edit'];
            }
            if (array_key_exists('delete', $adminConfig->managerConfiguration)) {
                $methodsMapping['delete'] = $adminConfig->managerConfiguration['delete'];
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

    protected function getDefaultActionTitle($title, $action)
    {
        $default = $title;

        if ($action == 'list') {
            $default = $this->inflectString($title) . 's List';
        } else if ($action == 'create') {
            $default = 'Create ' . $this->inflectString($title);
        } else if ($action == 'edit') {
            $default = 'Edit ' . $this->inflectString($title);
        }
        return $default;
    }

    /**
     * Return default actions configuration (list has exports, permissions are ROLE_ADMIN)
     *
     * @param $actionName
     * @return array
     */
    protected function getDefaultActionConfiguration($actionName)
    {
        $configuration = [];

        if ($actionName == 'list') {
            $configuration = [
                'title' => null,
                'fields' => $this->getDefaultFields(),
                'export' => ['json', 'xml', 'xls', 'csv', 'html'],
                'permissions' => ['ROLE_ADMIN']
            ];
        } else if ($actionName == 'create') {
            $configuration = [
                'title' => null,
                'fields' => $this->getDefaultFields(),
                'permissions' => ['ROLE_ADMIN'],
                'export' => []
            ];
        } else if ($actionName == 'edit') {
            $configuration = [
                'title' => null,
                'permissions' => ['ROLE_ADMIN'],
                'fields' => $this->getDefaultFields(),
                'export' => []
            ];
        } else if ($actionName == 'delete') {
            $configuration = [
                'title' => null,
                'fields' => $this->getDefaultFields(),
                'permissions' => ['ROLE_ADMIN'],
                'export' => []
            ];
        }
        return $configuration;
    }

    protected function getDefaultFields()
    {
        return [
            'id' => [],
            'label' => []
        ];
    }

    protected function getDefaultApplicationConfiguration()
    {
        return [
            'layout' => 'BlueBearAdminBundle::admin.layout.html.twig',
            'date_format' => 'd/m/Y',
            'routing' => [
                'name_pattern' => 'bluebear.admin.{admin}',
                'url_pattern' => '/{admin}/{action}',
             ]
        ];
    }

    /**
     * @return ApplicationConfiguration
     */
    public function getApplicationConfiguration()
    {
        return $this->applicationConfiguration;
    }
}
