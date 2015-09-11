<?php

namespace BlueBear\AdminBundle\Admin;

use Symfony\Component\PropertyAccess\PropertyAccess;

class Action
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var Field[]
     */
    protected $fields = [];

    /**
     * Action display among the fields
     *
     * @var Action[]
     */
    protected $fieldActions = [];

    /**
     * @var string[]
     */
    protected $permissions = [];

    protected $route;

    protected $parameters;

    /**
     * Configured linked actions to display in this view
     *
     * @var array
     */
    protected $actions = [];

    /**
     * Export types
     *
     * @var array
     */
    protected $export = [];

    /**
     * Array of properties to order by
     *
     * @var array
     */
    protected $order = [];

    /**
     * Icon class
     *
     * @var string
     */
    protected $icon;

    /**
     * @var
     */
    protected $target = '_self';

    protected $isParametersBuild = false;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Return true if action has a field named $fieldName
     *
     * @param $fieldName
     * @return bool
     */
    public function hasField($fieldName)
    {
        return array_key_exists($fieldName, $this->fields);
    }

    /**
     * @param Field[] $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param Field $field
     */
    public function addField(Field $field)
    {
        $this->fields[$field->getName()] = $field;
    }

    /**
     * @return string[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param string[] $permissions
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param mixed $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param mixed $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    public function buildParameters($entity)
    {
        $parameters = [];
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->parameters as $parameterName => $fieldName) {
            if ($this->isParametersBuild) {
                // TODO improve this code
                $parameters[$parameterName] = $accessor->getValue($entity, $parameterName);
            } else {
                if (is_array($fieldName) && !count($fieldName)) {
                    $fieldName = $parameterName;
                }
                $parameters[$parameterName] = $accessor->getValue($entity, $fieldName);
            }
        }
        $this->parameters = $parameters;
        $this->isParametersBuild = true;
    }

    /**
     * @return array
     */
    public function getExport()
    {
        return $this->export;
    }

    /**
     * @param array $export
     */
    public function setExport($export)
    {
        $this->export = $export;
    }

    /**
     * @return array
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param array $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return bool
     */
    public function hasOrder()
    {
        return count($this->order) > 0;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param array $actions
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
    }

    /**
     * @param Action $action
     */
    public function addAction(Action $action)
    {
        $this->actions[] = $action;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param mixed $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return Action[]
     */
    public function getFieldActions()
    {
        return $this->fieldActions;
    }

    /**
     * @param Action[] $fieldActions
     */
    public function setFieldActions($fieldActions)
    {
        $this->fieldActions = $fieldActions;
    }

    /**
     * @param Action $action
     */
    public function addFieldAction(Action $action)
    {
        $this->fieldActions[] = $action;
    }
}
