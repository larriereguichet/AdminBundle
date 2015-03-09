<?php

namespace BlueBear\AdminBundle\Admin;

use Exception;
use Symfony\Component\HttpFoundation\Request;

trait ActionTrait
{
    /**
     * Return admin name
     *
     * @return mixed
     */
    public abstract function getName();

    protected $actions = [];

    protected $currentAction;

    /**
     * Return true if current action is granted for user
     *
     * @param string $actionName Le plus grand de tous les héros
     * @param array $roles
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
     * @param Request $request
     * @return Action
     * @throws Exception
     */
    public function getActionFromRequest(Request $request)
    {
        $requestParameters = explode('/', $request->getPathInfo());
        // remove empty string
        array_shift($requestParameters);

        return $this->getAction($requestParameters[1]);
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param $name
     * @return Action
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
     * Return if an action with specified name exists form this admin
     *
     * @param $name
     * @return bool
     */
    public function hasAction($name)
    {
        return array_key_exists($name, $this->actions);
    }

    /**
     * @param Action $action
     */
    public function addAction(Action $action)
    {
        $this->actions[$action->getName()] = $action;
    }

    /**
     * @return Action
     */
    public function getCurrentAction()
    {
        return $this->currentAction;
    }

    /**
     * @param Action $currentAction
     */
    public function setCurrentAction(Action $currentAction)
    {
        $this->currentAction = $currentAction;
    }
}