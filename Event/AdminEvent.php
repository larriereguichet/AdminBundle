<?php

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\EventDispatcher\Event;

class AdminEvent extends Event
{
    const ADMIN_CREATE = 'event.admin.create';
    const ACTION_CREATE = 'event.action.create';

    protected $configuration;

    /**
     * Related Admin name.
     *
     * @var string
     */
    protected $adminName;

    /**
     * @var AdminInterface
     */
    protected $admin;

    /**
     * @var string
     */
    protected $actionName;

    /**
     * @param array $configuration
     * @return AdminEvent
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

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
    public function setAdmin($admin)
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
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;
    }

    /**
     * @param string $adminName
     * @return AdminEvent
     */
    public function setAdminName($adminName)
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
}
