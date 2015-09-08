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

    public function __construct(RoutingLoader $routingLoader, FieldFactory $fieldFactory)
    {
        $this->routingLoader = $routingLoader;
        $this->fieldFactory = $fieldFactory;
    }

    /**
     * Create an Action from configuration values
     *
     * @param $actionName
     * @param $actionConfiguration
     * @param Admin $admin
     * @return Action
     */
    public function create($actionName, $actionConfiguration, Admin $admin)
    {
        // resolving default options. Options are different according to action name
        $resolver = new OptionsResolver();
        $resolver->setDefaults($this->getDefaultActionConfiguration());
        $actionConfiguration = $resolver->resolve($actionConfiguration);
        // creating action object from configuration
        $action = new Action();
        $action->setName($actionName);
        $action->setTitle($actionConfiguration['title']);
        $action->setPermissions($actionConfiguration['permissions']);
        $action->setRoute($this->routingLoader->generateRouteName($action->getName(), $admin));
        $action->setExport($actionConfiguration['export']);
        $action->setOrder($actionConfiguration['order']);

        foreach ($actionConfiguration['actions'] as $customActionName => $customActionConfiguration) {
            $customAction = new Action();
            $customActionConfiguration = $resolver->resolve($customActionConfiguration);
            $customAction->setName($customActionName);
            $customAction->setTitle($customActionConfiguration['title']);
            $customAction->setRoute($customActionConfiguration['route']);
            $customAction->setIcon($customActionConfiguration['icon']);
            $customAction->setTarget($customActionConfiguration['target']);

            if (array_key_exists('parameters', $customActionConfiguration)) {
                $customAction->setParameters($customActionConfiguration['parameters']);
            }
            $action->addCustomAction($customAction);
        }
        // adding fields items to actions
        foreach ($actionConfiguration['fields'] as $fieldName => $fieldConfiguration) {
            $field = $this->fieldFactory->create($fieldName, $fieldConfiguration);
            $action->addField($field);
        }
        return $action;
    }

    protected function generateDefaultActionTitle($title, $action)
    {
        $default = $title;

        if ($action == 'list') {
            if (substr($title, strlen($title) - 1) == 'y') {
                $title = substr($title, 0, strlen($title) - 1) . 'ie';
            }
            $default = $this->inflectString($title) . 's List';
        } else if ($action == 'create') {
            $default = 'Create ' . $this->inflectString($title);
        } else if ($action == 'edit') {
            $default = 'Edit ' . $this->inflectString($title);
        } else if ($action == 'delete') {
            $default = 'Delete ' . $this->inflectString($title);
        }
        return $default;
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
            'export' => ['json', 'xml', 'xls', 'csv', 'html'],
            'order' => [],
            'actions' => [],
            'target' => '_self',
            'route' => '',
            'parameters' => [],
            'icon' => null,
        ];
        return $configuration;
    }
}
