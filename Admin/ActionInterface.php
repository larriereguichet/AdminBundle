<?php

namespace LAG\AdminBundle\Admin;

use LAG\AdminBundle\Admin\Configuration\ActionConfiguration;

interface ActionInterface
{
    /**
     * Return current action configuration
     *
     * @return ActionConfiguration
     */
    public function getConfiguration();

    /**
     * Return current action name
     *
     * @return mixed
     */
    public function getName();

    public function getTitle();

    public function getFields();

    public function getPermissions();

    /**
     * Return true if action has a field named $fieldName.
     *
     * @param $fieldName
     *
     * @return bool
     */
    public function hasField($fieldName);

    /**
     * @param Field[] $fields
     */
    public function setFields($fields);

    /**
     * @param Field $field
     */
    public function addField(Field $field);

    /**
     * @return Action[]
     */
    public function getActions();

    /**
     * @param array $actions
     */
    public function setActions($actions);

    /**
     * @param Action $action
     */
    public function addAction(Action $action);
}
