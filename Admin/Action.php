<?php

namespace LAG\AdminBundle\Admin;

use LAG\AdminBundle\Admin\Configuration\ActionConfiguration;

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
     * Configured linked actions to display in this view.
     *
     * @var array
     */
    protected $actions = [];

    /**
     * Actions displayed at the bottom of the view.
     *
     * @var array
     */
    protected $submitActions = [];

    protected $configuration;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var string[]
     */
    protected $batchActions = [];

    public function __construct($actionName, array $actionOptions, ActionConfiguration $configuration)
    {
        $this->configuration = $configuration;
        $this->name = $actionName;
        $this->title = $actionOptions['title'];
        $this->permissions = $actionOptions['permissions'];
        $this->submitActions = $actionOptions['submit_actions'];
        $this->batchActions = $actionOptions['batch'];
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
     * @param $fieldName
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
     * @return Action[]
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

    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * @return array
     */
    public function getSubmitActions()
    {
        return $this->submitActions;
    }

    /**
     * @param array $submitActions
     */
    public function setSubmitActions($submitActions)
    {
        $this->submitActions = $submitActions;
    }

    /**
     * @return string[]
     */
    public function getBatchActions()
    {
        return $this->batchActions;
    }

    /**
     * @param string[] $batchActions
     */
    public function setBatchActions($batchActions)
    {
        $this->batchActions = $batchActions;
    }

    /**
     * @return mixed
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
