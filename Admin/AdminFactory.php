<?php

namespace BlueBear\AdminBundle\Admin;

use BlueBear\AdminBundle\Manager\GenericManager;
use BlueBear\BaseBundle\Behavior\ContainerTrait;
use BlueBear\BaseBundle\Behavior\StringUtilsTrait;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * AdminFactory
 *
 * Create admin from configuration
 */
class AdminFactory
{
    use StringUtilsTrait, ContainerTrait;

    protected $admins = [];

    /**
     * Read configuration from container, then create admin with its actions and fields
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $admins = $this->getContainer()->getParameter('bluebear.admins');
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
        $requestParameters = explode('/', $request->getPathInfo());
        // remove empty string
        array_shift($requestParameters);
        // get configured admin
        $admin = $this->getAdmin($this->underscore($requestParameters[0]));
        // set current action
        $action = $admin->getActionFromRequest($request);
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
            throw new Exception('Invalid admin name "' . $name . '". Did you add it in config.yml ?');
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
    protected function createAdminFromConfig($adminName, array $adminConfigArray)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        // gathering admin data
        $adminConfig = new AdminConfig();
        $adminConfig->hydrateFromConfiguration($adminConfigArray, $this->getContainer());
        $entityRepository = $entityManager->getRepository($adminConfig->entityName);
        // create generic manager from configuration
        $entityManager = $this->createManagerFromConfig($adminConfig, $entityRepository);

        $admin = new Admin($adminName, $entityRepository, $entityManager, $adminConfig);
        // actions are optional
        if (!$adminConfig->actions) {
            $adminConfig->actions = $this->getDefaultActions();
        }
        // adding actions
        foreach ($adminConfig->actions as $actionName => $actionConfig) {
            $admin->addAction($this->createActionFromConfig($actionName, $actionConfig, $admin));
        }
        // adding admins to the pool
        $this->admins[$admin->getName()] = $admin;
    }

    /**
     * Create an Action from configuration values
     *
     * @param $actionName
     * @param $actionConfig
     * @param Admin $admin
     * @return Action
     */
    protected function createActionFromConfig($actionName, $actionConfig, Admin $admin)
    {
        // test each key to keep granularity in configuration
        if (array_key_exists('title', $actionConfig)) {
            $title = $actionConfig['title'];
        } else {
            // default title
            $title = $this->getDefaultActionTitle($admin->getName(), $actionName);
        }
        if (array_key_exists('permissions', $actionConfig)) {
            $permissions = $actionConfig['permissions'];
        } else {
            $permissions = $this->getDefaultPermissions();
        }
        if (array_key_exists('fields', $actionConfig)) {
            $fields = $actionConfig['fields'];
        } else {
            $fields = $this->getDefaultFields();
        }
        $action = new Action();
        $action->setName($actionName);
        $action->setTitle($title);
        $action->setPermissions($permissions);
        $action->setRoute($admin->generateRouteName($action->getName()));
        // adding items to actions
        foreach ($fields as $fieldName => $fieldConfig) {
            $field = new Field();
            $field->setName($fieldName);
            $field->setTitle($this->inflectString($fieldName));

            if (array_key_exists('length', $fieldConfig)) {
                $field->setLength($fieldConfig['length']);
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

    protected function getDefaultActions()
    {
        return [
            'list' => [],
            'create' => [],
            'edit' => [],
            'delete' => []
        ];
    }

    protected function getDefaultPermissions()
    {
        return [
            'ROLE_USER'
        ];
    }

    protected function getDefaultFields()
    {
        return [
            'id' => [],
            'label' => []
        ];
    }
}