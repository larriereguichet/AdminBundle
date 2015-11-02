<?php

namespace LAG\AdminBundle\Admin\Behaviors;

use Exception;
use LAG\AdminBundle\Admin\Action;
use LAG\AdminBundle\Admin\ActionInterface;

trait ActionTrait
{
    /**
     * Return admin name.
     *
     * @return mixed
     */
    abstract public function getName();

    /**
     * @var ActionInterface[]
     */
    protected $actions = [];

    /**
     * @var ActionInterface
     */
    protected $currentAction;

    /**
     * Return true if current action is granted for user.
     *
     * @param string $actionName Le plus grand de tous les héros
     * @param array  $roles
     *
     * @return bool
     */
    public function isActionGranted($actionName, array $roles)
    {
        $isGranted = array_key_exists($actionName, $this->actions);

        // if action exists
        if ($isGranted) {
            $isGranted = false;
            /** @var Action $action */
            $action = $this->actions[$actionName];
            // checking roles permissions
            foreach ($roles as $role) {
                if (in_array($role, $action->getPermissions())) {
                    $isGranted = true;
                }
            }
        }

        return $isGranted;
    }

    /**
     * @return ActionInterface[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param $name
     *
     * @return ActionInterface
     *
     * @throws Exception
     */
    public function getAction($name)
    {
        if (!array_key_exists($name, $this->getActions())) {
            throw new Exception("Invalid action name \"{$name}\" for admin '{$this->getName()}'");
        }

        return $this->actions[$name];
    }

    /**
     * Return if an action with specified name exists form this admin.
     *
     * @param $name
     *
     * @return bool
     */
    public function hasAction($name)
    {
        return array_key_exists($name, $this->actions);
    }

    /**
     * @param ActionInterface $action
     */
    public function addAction(ActionInterface $action)
    {
        $this->actions[$action->getName()] = $action;
    }

    /**
     * @return ActionInterface
     */
    public function getCurrentAction()
    {
        return $this->currentAction;
    }
}
