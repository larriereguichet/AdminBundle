<?php

namespace LAG\AdminBundle\Action\Event;

use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event dispatch before the creation of an action to allow third-party modification
 */
class BeforeConfigurationEvent extends Event
{
    /**
     * @var AdminInterface
     */
    protected $admin;

    /**
     * @var string
     */
    protected $actionName;

    /**
     * @var array
     */
    protected $actionConfiguration;

    /**
     * BeforeConfigurationEvent constructor.
     *
     * @param string $actionName
     * @param array $actionConfiguration
     * @param AdminInterface $admin
     */
    public function __construct($actionName, array $actionConfiguration, AdminInterface $admin)
    {
        $this->actionName = $actionName;
        $this->actionConfiguration = $actionConfiguration;
        $this->admin = $admin;
    }

    /**
     * @return AdminInterface
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * @return array
     */
    public function getActionConfiguration()
    {
        return $this->actionConfiguration;
    }

    /**
     * @param array $actionConfiguration
     */
    public function setActionConfiguration($actionConfiguration)
    {
        $this->actionConfiguration = $actionConfiguration;
    }
}
