<?php

namespace LAG\AdminBundle\Admin\Event;

use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\EventDispatcher\Event;

class AdminEvent extends Event
{
    /**
     * Array containing the configuration of each Admin
     *
     * @var array
     */
    protected $adminsConfiguration = [];

    /**
     * Array containing the configuration of the related Admin
     *
     * @var array
     */
    protected $adminConfiguration = [];

    /**
     * Related Action configuration
     *
     * @var array
     */
    protected $actionConfiguration = [];

    /**
     * Related Admin name.
     *
     * @var string
     */
    protected $adminName;

    /**
     * Related Action name
     * 
     * @var string
     */
    protected $actionName;

    /**
     * @var AdminInterface
     */
    protected $admin;

    /**
     * @var ActionInterface
     */
    protected $action;

    /**
     * @return AdminInterface
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @param AdminInterface $admin
     * @return $this
     */
    public function setAdmin($admin = null)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * @param string $actionName
     * @return $this
     */
    public function setActionName($actionName = null)
    {
        $this->actionName = $actionName;

        return $this;
    }

    /**
     * @param string $adminName
     * @return AdminEvent
     */
    public function setAdminName($adminName = null)
    {
        $this->adminName = $adminName;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdminName()
    {
        return $this->adminName;
    }

    /**
     * @return ActionInterface
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param ActionInterface $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return array
     */
    public function getAdminsConfiguration()
    {
        return $this->adminsConfiguration;
    }

    /**
     * @param array $adminsConfiguration
     * @return $this
     */
    public function setAdminsConfiguration($adminsConfiguration = [])
    {
        $this->adminsConfiguration = $adminsConfiguration;

        return $this;
    }

    /**
     * @return array
     */
    public function getAdminConfiguration()
    {
        return $this->adminConfiguration;
    }

    /**
     * @param array $adminConfiguration
     * @return $this
     */
    public function setAdminConfiguration($adminConfiguration = [])
    {
        $this->adminConfiguration = $adminConfiguration;

        return $this;
    }

    /**
     * @param array $actionConfiguration
     * @return $this
     */
    public function setActionConfiguration($actionConfiguration)
    {
        $this->actionConfiguration = $actionConfiguration;

        return $this;
    }

    /**
     * @return array
     */
    public function getActionConfiguration()
    {
        return $this->actionConfiguration;
    }
}
