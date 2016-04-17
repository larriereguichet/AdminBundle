<?php

namespace LAG\AdminBundle\Admin;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Field\Field;

class Action implements ActionInterface
{
    /**
     * Action name.
     *
     * @var string
     */
    protected $name;

    /**
     * Action title.
     *
     * @var string
     */
    protected $title;

    /**
     * Fields displayed for this action.
     *
     * @var Field[]
     */
    protected $fields = [];

    /**
     * Action permissions.
     *
     * @var string[]
     */
    protected $permissions = [];

    /**
     * @var ActionConfiguration
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * Action constructor.
     *
     * @param string $actionName
     * @param ActionConfiguration $configuration
     */
    public function __construct($actionName, ActionConfiguration $configuration)
    {
        $this->configuration = $configuration;
        $this->name = $actionName;
        $this->title = $configuration->getParameter('title');
        $this->permissions = $configuration->getParameter('permissions');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Return true if action has a field named $fieldName.
     *
     * @param string $fieldName
     *
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
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param array $filters
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    /**
     * @param Filter $filter
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
    }
    
    /**
     * @return ActionConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
