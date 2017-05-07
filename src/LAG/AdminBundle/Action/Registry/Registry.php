<?php

namespace LAG\AdminBundle\Action\Registry;

use Exception;
use LAG\AdminBundle\Action\ActionInterface;

class Registry
{
    /**
     * Actions registry.
     *
     * @var ActionInterface[]
     */
    protected $actions = [];
    
    /**
     * Add an Action to the registry.
     *
     * @param $id
     * @param ActionInterface $action
     *
     * @throws Exception
     */
    public function add($id, ActionInterface $action)
    {
        if (array_key_exists($id, $this->actions)) {
            throw new Exception('An Action with the name "'.$id.'" has already been registered');
        }
        $this->actions[$id] = $action;
    }
    
    /**
     * Return an Action from the registry.
     *
     * @param string $id
     * @return ActionInterface
     * @throws Exception
     */
    public function get($id)
    {
        if (!array_key_exists($id, $this->actions)) {
            throw new Exception('No Action with the service id "'.$id.'" has been found');
        }
        
        return $this->actions[$id];
    }
    
    /**
     * Return true if an Action with the name $id has been registered.
     *
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return array_key_exists($id, $this->actions);
    }
    
    /**
     * Return all the registered Actions.
     *
     * @return ActionInterface[]
     */
    public function all()
    {
        return $this->actions;
    }
}
