<?php

namespace BlueBear\AdminBundle\Admin;

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
     * @var string[]
     */
    protected $permissions = [];

    protected $route;

    protected $parameters;

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
}
