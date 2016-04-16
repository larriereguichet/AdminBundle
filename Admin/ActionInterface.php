<?php

namespace LAG\AdminBundle\Admin;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Field\Field;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @return string
     */
    public function getName();

    /**
     * Return action title (title is the displayed to the user whereas name is not)
     *
     * @return mixed
     */
    public function getTitle();

    /**
     * Return action fields
     *
     * @return Field[]
     */
    public function getFields();

    /**
     * Return action permissions
     *
     * @return array
     * @throws NotFoundHttpException
     */
    public function getPermissions();

    /**
     * Return true if action has a field named $fieldName
     *
     * @param $fieldName
     * @return bool
     */
    public function hasField($fieldName);

    /**
     * Define fields for actions
     *
     * @param Field[] $fields
     */
    public function setFields($fields);

    /**
     * Add a field to action
     *
     * @param Field $field
     */
    public function addField(Field $field);
}
