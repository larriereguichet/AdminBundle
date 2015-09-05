<?php

namespace BlueBear\AdminBundle\Admin;

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

    public function __construct(RoutingLoader $routingLoader)
    {
        $this->routingLoader = $routingLoader;
    }

    /**
     * Create an Action from configuration values
     *
     * @param $actionName
     * @param $actionConfiguration
     * @param Admin $admin
     * @return Action
     */
    public function createActionFromConfig($actionName, $actionConfiguration, Admin $admin)
    {
        // resolving default options. Options are different according to action name
        $resolver = new OptionsResolver();
        $resolver->setDefaults($this->getDefaultActionConfiguration($actionName, $admin->getName()));
        $actionConfiguration = $resolver->resolve($actionConfiguration);
        // creating action object from configuration
        $action = new Action();
        $action->setName($actionName);
        $action->setTitle($actionConfiguration['title']);
        $action->setPermissions($actionConfiguration['permissions']);
        $action->setRoute($this->routingLoader->generateRouteName($action->getName(), $admin));
        $action->setExport($actionConfiguration['export']);
        $action->setOrder($actionConfiguration['order']);
        // adding fields items to actions
        foreach ($actionConfiguration['fields'] as $fieldName => $fieldConfiguration) {
            $field = new Field();
            $field->setName($fieldName);
            $field->setTitle($this->inflectString($fieldName));

            if (is_array($fieldConfiguration) && array_key_exists('length', $fieldConfiguration)) {
                $field->setLength($fieldConfiguration['length']);
            }
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
     * @param $actionName
     * @param $adminName
     * @return array
     */
    protected function getDefaultActionConfiguration($actionName, $adminName)
    {
        $configuration = [
            'title' => $this->generateDefaultActionTitle($adminName, $actionName),
            'fields' => $this->getDefaultFields(),
            'permissions' => ['ROLE_ADMIN'],
            'export' => [],
            'order' => []
        ];
        if ($actionName == 'list') {
            $configuration = array_merge($configuration, [
                'export' => ['json', 'xml', 'xls', 'csv', 'html']
            ]);
        }
        return $configuration;
    }

    protected function getDefaultFields()
    {
        return [
            'id' => []
        ];
    }
}
