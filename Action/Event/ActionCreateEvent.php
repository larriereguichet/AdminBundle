<?php

namespace LAG\AdminBundle\Action\Event;

use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\EventDispatcher\Event;

class ActionCreateEvent extends Event
{
    /**
     * @var array
     */
    protected $actionConfiguration;

    /**
     * @var string
     */
    protected $actionName;

    /**
     * @var AdminInterface
     */
    protected $admin;

    /**
     * ActionCreateEvent constructor.
     *
     * @param string $actionName
     * @param array $actionConfiguration
     * @param AdminInterface $admin
     */
    public function __construct($actionName, array $actionConfiguration, AdminInterface $admin)
    {
        $this->actionConfiguration = $actionConfiguration;
        $this->actionName = $actionName;
        $this->admin = $admin;
    }

    /**
     * @return array
     */
    public function getActionConfiguration()
    {
        return $this->actionConfiguration;
    }

    /**
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * @return AdminInterface
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @param array $actionConfiguration
     */
    public function setActionConfiguration($actionConfiguration)
    {
        $this->actionConfiguration = $actionConfiguration;
    }
}
