<?php

namespace LAG\AdminBundle\Action\Event;

use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event dispatch before the creation of an action to allow third-party modification.
 */
class BeforeConfigurationEvent extends Event
{
    /**
     * @var string
     */
    private $adminName;

    /**
     * @var string
     */
    private $actionName;

    /**
     * @var array
     */
    private $actionConfiguration;

    /**
     * @var AdminConfiguration
     */
    private $adminConfiguration;

    /**
     * BeforeConfigurationEvent constructor.
     *
     * @param string             $actionName
     * @param array              $actionConfiguration
     * @param string             $adminName
     * @param AdminConfiguration $adminConfiguration
     */
    public function __construct(
        $actionName,
        array $actionConfiguration,
        $adminName,
        AdminConfiguration $adminConfiguration
    ) {
        $this->actionName = $actionName;
        $this->actionConfiguration = $actionConfiguration;
        $this->adminName = $adminName;
        $this->adminConfiguration = $adminConfiguration;
    }

    /**
     * @return string
     */
    public function getAdminName()
    {
        return $this->adminName;
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

    /**
     * @return AdminConfiguration
     */
    public function getAdminConfiguration()
    {
        return $this->adminConfiguration;
    }
}
