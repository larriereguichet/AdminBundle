<?php

namespace BlueBear\AdminBundle\Admin\Factory;

use BlueBear\AdminBundle\Admin\Action;
use BlueBear\AdminBundle\Admin\Admin;
use BlueBear\AdminBundle\Routing\RoutingLoader;
use BlueBear\BaseBundle\Behavior\StringUtilsTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionFactory
{
    use StringUtilsTrait;

    /**
     * @var RoutingLoader
     */
    protected $routingLoader;

    /**
     * @var FieldFactory
     */
    protected $fieldFactory;

    /**
     * @var FilterFactory
     */
    protected $filterFactory;

    public function __construct(RoutingLoader $routingLoader, FieldFactory $fieldFactory, FilterFactory $filterFactory)
    {
        $this->routingLoader = $routingLoader;
        $this->fieldFactory = $fieldFactory;
        $this->filterFactory = $filterFactory;
    }

    /**
     * Create an Action from configuration values
     *
     * @param string $actionName
     * @param array $actionConfiguration
     * @param Admin $admin
     * @return Action
     */
    public function create($actionName, array $actionConfiguration, Admin $admin)
    {
        // resolving default options
        $resolver = new OptionsResolver();
        $resolver->setDefaults($this->getDefaultActionConfiguration());
        $actionConfiguration = $resolver->resolve($actionConfiguration);
        // creating action object from configuration
        $action = $this->createActionFromConfiguration($actionConfiguration, $actionName, $admin);

        foreach ($actionConfiguration['actions'] as $customActionName => $customActionConfiguration) {
            // resolve configuration
            $customActionConfiguration = $resolver->resolve($customActionConfiguration);
            // create action
            $customAction = $this->createActionFromConfiguration($customActionConfiguration, $customActionName);
            // add to the main action
            $action->addAction($customAction);
        }
        foreach ($actionConfiguration['field_actions'] as $customActionName => $customActionConfiguration) {
            // resolve configuration
            $customActionConfiguration = $resolver->resolve($customActionConfiguration);
            // create action
            $customAction = $this->createActionFromConfiguration($customActionConfiguration, $customActionName);
            // add to the main action
            $action->addFieldAction($customAction);
        }
        // adding fields items to actions
        foreach ($actionConfiguration['fields'] as $fieldName => $fieldConfiguration) {
            $field = $this->fieldFactory->create($fieldName, $fieldConfiguration);
            $action->addField($field);
        }
        // adding filters to the action
        foreach ($actionConfiguration['filters'] as $fieldName => $filterConfiguration) {
            $filter = $this->filterFactory->create($fieldName, $filterConfiguration);
            $action->addFilter($filter);
        }
        return $action;
    }

    protected function createActionFromConfiguration(array $actionConfiguration, $actionName, Admin $admin = null)
    {
        $action = new Action();
        $action->setName($actionName);
        $action->setTitle($actionConfiguration['title']);
        $action->setPermissions($actionConfiguration['permissions']);
        $action->setExport($actionConfiguration['export']);
        $action->setOrder($actionConfiguration['order']);
        $action->setRoute($actionConfiguration['route']);
        $action->setIcon($actionConfiguration['icon']);
        $action->setTarget($actionConfiguration['target']);
        $action->setParameters($actionConfiguration['parameters']);

        if ($admin && !$actionConfiguration['route']) {
            $action->setRoute($this->routingLoader->generateRouteName($actionName, $admin));
        }
        if (!$action->getTitle()) {
            $adminName = ($admin) ? $admin->getName() . '.' : '';
            $action->setTitle(sprintf('bluebear.admin.%s%s', $adminName, $actionName));
        }
        return $action;
    }

    /**
     * Return default actions configuration (list has exports, permissions are ROLE_ADMIN)
     *
     * @return array
     */
    protected function getDefaultActionConfiguration()
    {
        $configuration = [
            'title' => null,
            'fields' => [
                'id' => []
            ],
            'field_actions' => [],
            'permissions' => ['ROLE_ADMIN'],
            'export' => [],
            'order' => [],
            'actions' => [],
            'target' => '_self',
            'route' => '',
            'parameters' => [],
            'icon' => null,
            'filters' => []
        ];
        return $configuration;
    }
}
